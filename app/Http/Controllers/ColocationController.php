<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColocationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $colocation = $user->ownedColocations()->first();
        return view('colocations.index', compact('colocation'));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->ownedColocations()->exists(), 403, 'You already own a colocation');
        return view('colocations.create');
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->ownedColocations()->exists(), 403, 'You already own a colocation');

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $colocation = Colocation::create([
            'name' => $request->name,
            'status' => 'active',
            'owner_id' => Auth::id(),
        ]);

        $colocation->members()->attach(Auth::id(), [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.index');
    }

    public function show(Colocation $colocation)
    {
        abort_if($colocation->owner_id !== Auth::id(), 403);
        return view('colocations.show', compact('colocation'));
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
        $colocation->delete();
        return redirect()->route('colocations.index');
    }
}
