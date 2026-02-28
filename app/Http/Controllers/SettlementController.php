<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettlementController extends Controller
{
    public function index(Colocation $colocation)
    {
        $user = Auth::user();
        $isMember = $colocation->members()->where('user_id', $user->id)->wherePivot('left_at', null)->exists();
        abort_if(!$isMember, 403);

        $settlements = $this->calculateSettlements($colocation);
        
        // If not owner, filter to show only settlements involving this user
        if ($colocation->owner_id !== $user->id) {
            $settlements = $settlements->filter(function($settlement) use ($user) {
                return $settlement['payer']->id === $user->id || $settlement['receiver']->id === $user->id;
            });
        }

        return view('settlements.index', compact('colocation', 'settlements'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        $user = Auth::user();
        abort_if($colocation->owner_id !== $user->id, 403);

        $request->validate([
            'payer_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:payer_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        Settlement::create([
            'colocation_id' => $colocation->id,
            'payer_id' => $request->payer_id,
            'receiver_id' => $request->receiver_id,
            'amount' => $request->amount,
        ]);

        return back()->with('success', 'Settlement created successfully');
    }

    public function markAsPaid(Colocation $colocation, Settlement $settlement)
    {
        $user = Auth::user();
        abort_if($settlement->receiver_id !== $user->id && $colocation->owner_id !== $user->id, 403);

        $settlement->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Settlement marked as paid');
    }

    private function calculateSettlements(Colocation $colocation)
    {
        $members = $colocation->members()->wherePivot('left_at', null)->get();
        $memberCount = $members->count();

        if ($memberCount === 0) {
            return collect();
        }

        $expenses = $colocation->expenses()->get();
        $balances = [];

        foreach ($members as $member) {
            $balances[$member->id] = 0;
        }

        foreach ($expenses as $expense) {
            $sharePerMember = $expense->amount / $memberCount;
            
            if (isset($balances[$expense->user_id])) {
                $balances[$expense->user_id] += $expense->amount - $sharePerMember;
            }

            foreach ($members as $member) {
                if ($member->id !== $expense->user_id && isset($balances[$member->id])) {
                    $balances[$member->id] -= $sharePerMember;
                }
            }
        }

        $settlements = collect();
        $creditors = collect($balances)->filter(fn($balance) => $balance > 0.01)->sortDesc();
        $debtors = collect($balances)->filter(fn($balance) => $balance < -0.01)->sort();

        foreach ($debtors as $debtorId => $debtAmount) {
            foreach ($creditors as $creditorId => $creditAmount) {
                if (abs($debtAmount) < 0.01 || $creditAmount < 0.01) {
                    continue;
                }

                $settlementAmount = min(abs($debtAmount), $creditAmount);

                $settlements->push([
                    'payer' => $members->firstWhere('id', $debtorId),
                    'receiver' => $members->firstWhere('id', $creditorId),
                    'amount' => round($settlementAmount, 2),
                ]);

                $debtAmount += $settlementAmount;
                $creditors[$creditorId] -= $settlementAmount;
            }
        }

        return $settlements;
    }
}
