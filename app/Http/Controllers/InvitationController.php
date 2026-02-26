<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function send(Request $request, Colocation $colocation)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($colocation->owner_id !== $user->id, 403);

        $request->validate([
            'email' => 'required|email',
        ]);

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $request->email,
            'token' => Invitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::raw(
            "You've been invited to join {$colocation->name}. Click here: " . url("/invitations/{$invitation->token}"),
            function ($message) use ($request) {
                $message->to($request->email)->subject('Colocation Invitation');
            }
        );

        return back()->with('success', 'Invitation sent successfully');
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect('/')->with('error', 'This invitation has expired');
        }

        if ($invitation->isAccepted()) {
            return redirect('/')->with('error', 'This invitation has already been used');
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept($token)
    {
        /** @var User $user */
        $user = Auth::user();
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect('/')->with('error', 'This invitation has expired');
        }

        if ($invitation->isAccepted()) {
            return redirect('/')->with('error', 'This invitation has already been used');
        }

        if ($user->colocations()->wherePivot('left_at', null)->exists()) {
            return redirect('/')->with('error', 'You already have an active colocation membership');
        }

        $invitation->colocation->members()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('colocations.index')->with('success', 'You have joined the colocation');
    }
}
