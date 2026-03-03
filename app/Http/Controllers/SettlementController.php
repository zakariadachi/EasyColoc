<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    public function index(Colocation $colocation)
    {
        $user = Auth::user();
        $isMember = $colocation->members()->where('user_id', $user->id)->wherePivot('left_at', null)->exists();
        abort_if(!$isMember, 403);

        $paymentHistory = Settlement::where('colocation_id', $colocation->id)
            ->where('is_paid', true)
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('settlements.index', compact('colocation', 'paymentHistory'));
    }
}
