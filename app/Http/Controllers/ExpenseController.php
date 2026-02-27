<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $user = Auth::user();
        abort_if($colocation->owner_id !== $user->id, 403, 'Only the owner can add expenses');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return back()->with('success', 'Expense added successfully');
    }

    public function update(Request $request, Colocation $colocation, Expense $expense)
    {
        $user = Auth::user();
        abort_if($expense->user_id !== $user->id && $colocation->owner_id !== $user->id, 403);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        $expense->update($request->only('category_id', 'description', 'amount', 'date'));

        return back()->with('success', 'Expense updated successfully');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $user = Auth::user();
        abort_if($colocation->owner_id !== $user->id, 403, 'Only the owner can delete expenses');

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully');
    }
}
