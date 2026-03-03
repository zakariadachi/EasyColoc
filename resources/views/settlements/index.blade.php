@extends('layouts.dashboard')

@section('page-title', 'Historique des paiements - ' . $colocation->name)

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 text-white p-8 rounded-xl">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">Historique des paiements</h1>
                <p class="text-emerald-100">{{ $colocation->name }}</p>
            </div>
            <a href="{{ route('colocations.show', $colocation) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg">
                Retour
            </a>
        </div>
    </div>

    @if($paymentHistory->isEmpty())
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <div class="text-center py-8 text-slate-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-semibold">Aucun historique</p>
                <p class="text-sm">Aucun paiement effectué pour le moment</p>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h3 class="text-lg font-bold mb-4">Tous les paiements effectués</h3>
            <div class="space-y-2">
                @foreach($paymentHistory as $payment)
                    <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm">
                                <span class="font-bold">{{ number_format($payment->amount, 2) }} DH</span>
                            </p>
                        </div>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
