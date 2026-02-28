<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $colocations = $user->colocations()->wherePivot('left_at', null)->get();
        $colocations->load('members');
        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $hasActiveOwned = $user->ownedColocations()
            ->where('status', 'active')
            ->whereHas('members', function($q) use ($user) {
                $q->where('user_id', $user->id)->whereNull('left_at');
            })->exists();
        
        abort_if($hasActiveOwned, 403, 'You already own an active colocation');
        return view('colocations.create');
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
        
        $month = $request->get('month', now()->format('Y-m'));
        
        $expenses = $colocation->expenses()
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->with(['user', 'category'])
            ->orderBy('date', 'desc')
            ->get();
        
        $categories = Category::whereNull('colocation_id')
            ->orWhere('colocation_id', $colocation->id)
            ->get();
        
        $stats = $expenses->groupBy('category_id')->map(function($items) {
            return $items->sum('amount');
        });
        
        return view('colocations.show', compact('colocation', 'expenses', 'categories', 'month', 'stats'));
    }

    public function edit(Colocation $colocation)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);
        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,cancelled',
        ]);

        $colocation->update($request->only('name', 'status'));

        return redirect()->route('colocations.index');
    }

    public function destroy(Colocation $colocation)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);
        
        $activeMembersCount = $colocation->members()->wherePivot('left_at', null)->where('user_id', '!=', Auth::id())->count();
        
        if ($activeMembersCount > 0) {
            return back()->with('error', 'Cannot close colocation while there are active members. Remove all members first.');
        }
        
        $colocation->members()->updateExistingPivot(Auth::id(), ['left_at' => now()]);
        $colocation->update(['status' => 'cancelled']);
        
        return redirect()->route('colocations.index')->with('success', 'Colocation closed successfully');
    }

    public function leave(Colocation $colocation)
    {
        $user = Auth::user();
        
        if ($colocation->owner_id === $user->id) {
            return back()->with('error', 'Owner cannot leave the colocation');
        }

        // Calculate user's balance before leaving
        $balance = $this->calculateUserBalance($colocation, $user->id);
        
        // Update reputation based on balance
        if ($balance < -0.01) {
            // User owes money
            $user->reputation = $user->reputation - 1;
            $user->save();
        } else {
            // User is owed or balanced
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

        // Calculate member's balance before removal
        $balance = $this->calculateUserBalance($colocation, $member->id);
        
        // Update reputation based on balance
        if ($balance < -0.01) {
            // Member owes money - decrease reputation
            $member->reputation = $member->reputation - 1;
            $member->save();
        } else {
            // Member is owed or balanced
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
        $members = $colocation->members()->wherePivot('left_at', null)->get();
        $memberCount = $members->count();

        if ($memberCount === 0) {
            return 0;
        }

        $expenses = $colocation->expenses()->get();
        $balance = 0;

        foreach ($expenses as $expense) {
            $sharePerMember = $expense->amount / $memberCount;
            
            if ($expense->user_id === $userId) {
                $balance += $expense->amount - $sharePerMember;
            } else {
                $balance -= $sharePerMember;
            }
        }

        return $balance;
    }
}
