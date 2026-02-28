<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_colocations' => Colocation::count(),
            'active_colocations' => Colocation::where('status', 'active')->count(),
            'cancelled_colocations' => Colocation::where('status', 'cancelled')->count(),
            'total_expenses' => Expense::count(),
            'banned_users' => User::where('is_banned', true)->count(),
        ];

        $expensesByCategory = Expense::select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return view('admin.index', compact('stats', 'expensesByCategory'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function banUser(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Cannot ban an admin');
        }

        $user->update(['is_banned' => true]);
        
        // Log out the user if they are currently logged in
        Auth::logout();

        return back()->with('success', 'User banned successfully');
    }

    public function unbanUser(User $user)
    {
        $user->update(['is_banned' => false]);
        return back()->with('success', 'User unbanned successfully');
    }
}
