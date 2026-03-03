<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $user = Auth::user();
        
        // Check if user is member of colocation
        abort_if(!$colocation->members->contains($user->id) && $colocation->owner_id !== $user->id, 403);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        // Owner can choose who paid, members automatically set to themselves
        $paidBy = $colocation->owner_id === $user->id && $request->has('user_id') 
            ? $request->user_id 
            : $user->id;

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $paidBy,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return back()->with('success', 'Expense added successfully');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $user = Auth::user();
        abort_if($expense->user_id !== $user->id && $colocation->owner_id !== $user->id, 403);

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully');
    }

    public function payShare(Request $request, Colocation $colocation, Expense $expense)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
        ]);

        $memberId = $request->member_id;
        $userId = Auth::id();
        $ownerId = $colocation->owner_id;

        // Debug logging
        Log::info('PayShare Debug', [
            'member_id' => $memberId,
            'user_id' => $userId,
            'owner_id' => $ownerId,
            'member_id_type' => gettype($memberId),
            'user_id_type' => gettype($userId),
            'comparison' => $memberId === $userId,
        ]);

        // Check if user is a member of the colocation
        $isMember = $colocation->members()->where('user_id', $userId)->wherePivot('left_at', null)->exists();
        abort_if(!$isMember, 403, 'You are not a member of this colocation');

        // Authorization: Member can pay their own share OR Owner can mark any share as paid
        $canPay = ($memberId == $userId) || ($ownerId == $userId);
        abort_if(!$canPay, 403, 'Unauthorized: You can only pay your own share or mark payments as owner');

        // Prevent duplicate payments
        $exists = \App\Models\Settlement::where('expense_id', $expense->id)
            ->where('payer_id', $memberId)
            ->where('is_paid', true)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'This share has already been paid.');
        }

        // Create a settlement record
        \App\Models\Settlement::create([
            'colocation_id' => $colocation->id,
            'payer_id' => $memberId,
            'receiver_id' => $expense->user_id,
            'expense_id' => $expense->id,
            'amount' => $expense->amount / $colocation->members->count(),
            'is_paid' => true,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Payment recorded successfully');
    }
}
