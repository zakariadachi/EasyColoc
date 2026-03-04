<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Category;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Settlement;

class ColocationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $colocations = $user->colocations()->wherePivot('left_at', null)->get();
        $colocations->load(['members' => function($query) {
            $query->wherePivot('left_at', null);
        }]);
        return view('colocations.index', compact('colocations'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->colocations()->wherePivot('left_at', null)->exists()) {
            return back()->with('error', 'You already have an active colocation');
        }

        $request->validate(['name' => 'required|string|max:255']);

        $colocation = Colocation::create([
            'name' => $request->name,
            'status' => 'active',
            'owner_id' => Auth::id(),
        ]);

        $existingMembership = DB::table('colocation_user')->where('user_id', Auth::id())->first();
        
        if ($existingMembership) {
            DB::table('colocation_user')->where('user_id', Auth::id())->update([
                'colocation_id' => $colocation->id,
                'role' => 'owner',
                'joined_at' => now(),
                'left_at' => null,
            ]);
        } else {
            $colocation->members()->attach(Auth::id(), [
                'role' => 'owner',
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('colocations.index');
    }

    public function show(Request $request, Colocation $colocation)
    {
        $user = Auth::user();
        $isMember = $colocation->members()->where('user_id', $user->id)->wherePivot('left_at', null)->exists();
        abort_if(!$isMember, 403);
        
        $colocation->load(['members' => function($query) {
            $query->wherePivot('left_at', null);
        }]);
        
        $month = $request->get('month', 'all');
        
        $expensesQuery = $colocation->expenses()->with(['user', 'category']);
        
        if ($month !== 'all' && $month !== null) {
            $expensesQuery->whereYear('date', substr($month, 0, 4))
                         ->whereMonth('date', substr($month, 5, 2));
        }
        
        $expenses = $expensesQuery->orderBy('date', 'desc')->get();
        
        $categories = Category::whereNull('colocation_id')
            ->orWhere('colocation_id', $colocation->id)
            ->get();
        
        $stats = $expenses->groupBy('category_id')->map(function($items) {
            return $items->sum('amount');
        });

        $settledShares = Settlement::where('colocation_id', $colocation->id)
            ->whereNotNull('expense_id')
            ->where('is_paid', true)
            ->get()
            ->keyBy(fn($s) => $s->expense_id . '_' . $s->payer_id);
        
        // Calculate balances
        $balances = [];
        foreach ($colocation->members as $member) {
            $balances[$member->id] = $this->calculateUserBalance($colocation, $member->id);
        }
        
        return view('colocations.show', compact('colocation', 'expenses', 'categories', 'month', 'stats', 'settledShares', 'balances'));
    }

    public function destroy(Colocation $colocation)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);
        
        $activeMembersCount = $colocation->members()->wherePivot('left_at', null)->where('user_id', '!=', Auth::id())->count();
        
        if ($activeMembersCount > 0) {
            return back()->with('error', 'Cannot close colocation while there are active members. Remove all members first.');
        }
        
        /** @var User $user */
        $user = Auth::user();
        $balance = $this->calculateUserBalance($colocation, $user->id);
        
        if ($balance < -0.01) {
            $user->reputation = $user->reputation - 1;
            $user->save();
        } else {
            $user->reputation = $user->reputation + 1;
            $user->save();
        }
        
        $colocation->members()->updateExistingPivot(Auth::id(), ['left_at' => now()]);
        $colocation->update(['status' => 'cancelled']);
        
        return redirect()->route('colocations.index')->with('success', 'Colocation closed successfully');
    }

    public function leave(Colocation $colocation)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($colocation->owner_id === $user->id) {
            return back()->with('error', 'Owner cannot leave the colocation');
        }

        $balance = $this->calculateUserBalance($colocation, $user->id);
        
        if ($balance < -0.01) {
            $user->reputation = $user->reputation - 1;
            $user->save();
        } else {
            $user->reputation = $user->reputation + 1;
            $user->save();
        }

        $colocation->members()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return redirect()->route('colocations.index')->with('success', 'You have left the colocation');
    }

    public function removeMember(Colocation $colocation, User $member)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);

        if ($colocation->owner_id === $member->id) {
            return back()->with('error', 'Cannot remove the owner');
        }

        $balance = $this->calculateUserBalance($colocation, $member->id);
        
        // Updat reputation
        if ($balance < -0.01) {
            $member->reputation = $member->reputation - 1;
            $member->save();
            
            // Transfer debt to owner
            $owner = User::find($colocation->owner_id);
            $defaultCategory = Category::whereNull('colocation_id')->first();
            
            Expense::create([
                'colocation_id' => $colocation->id,
                'user_id' => $owner->id,
                'category_id' => $defaultCategory->id,
                'description' => "Ajustement - Dette de {$member->name} transférée",
                'amount' => abs($balance),
                'date' => now(),
            ]);
        } else {
            $member->reputation = $member->reputation + 1;
            $member->save();
        }

        $colocation->members()->updateExistingPivot($member->id, [
            'left_at' => now(),
        ]);

        return back()->with('success', 'Member removed successfully');
    }

    private function calculateUserBalance(Colocation $colocation, int $userId)
    {
        $expenses = $colocation->expenses()->get();
        $balance = 0;

        foreach ($expenses as $expense) {
            $activeMembersAtExpenseTime = $colocation->members()
                ->where(function($q) use ($expense) {
                    $q->where('colocation_user.joined_at', '<=', $expense->date)
                      ->where(function($q2) use ($expense) {
                          $q2->whereNull('colocation_user.left_at')
                             ->orWhere('colocation_user.left_at', '>', $expense->date);
                      });
                })
                ->pluck('user_id');
            
            if (!$activeMembersAtExpenseTime->contains($userId)) {
                continue;
            }
            
            $memberCount = $activeMembersAtExpenseTime->count();
            $sharePerMember = $expense->amount / $memberCount;
            
            if ($expense->user_id === $userId) {
                // User paid the expense initially
                $balance += $expense->amount - $sharePerMember;
                
                $paymentsReceived = Settlement::where('expense_id', $expense->id)
                    ->where('receiver_id', $userId)
                    ->where('is_paid', true)
                    ->sum('amount');
                
                $balance -= $paymentsReceived;
            } else {
                // Check user paid
                $alreadyPaid = Settlement::where('expense_id', $expense->id)
                    ->where('payer_id', $userId)
                    ->where('is_paid', true)
                    ->exists();
                
                if (!$alreadyPaid) {
                    $balance -= $sharePerMember;
                }
            }
        }

        return $balance;
    }
}
