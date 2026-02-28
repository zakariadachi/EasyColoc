@extends('layouts.dashboard')

@section('page-title', 'Règlements - ' . $colocation->name)

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 text-white p-8 rounded-xl">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">Règlements</h1>
                <p class="text-emerald-100">{{ $colocation->name }}</p>
                @if($colocation->owner_id !== auth()->id())
                    <p class="text-emerald-200 text-sm mt-1">Vos règlements personnels</p>
                @endif
            </div>
            <a href="{{ route('colocations.show', $colocation) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg">
                Retour
            </a>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200">
        <h3 class="text-lg font-bold mb-2">
            @if($colocation->owner_id === auth()->id())
                Qui doit payer qui ?
            @else
                Mes règlements
            @endif
        </h3>
        <p class="text-sm text-slate-600 mb-4">
            @if($colocation->owner_id === auth()->id())
                Voici la liste des paiements à effectuer entre les membres pour équilibrer les dépenses de la colocation.
            @else
                Voici les paiements que vous devez effectuer ou recevoir pour équilibrer votre part des dépenses.
            @endif
        </p>
        
        @if($settlements->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg font-semibold">Tout est réglé !</p>
                <p class="text-sm">Aucun règlement en attente</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($settlements as $settlement)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($settlement['payer']->name, 0, 2)) }}
                                </div>
                                <span class="font-semibold">{{ $settlement['payer']->name }}</span>
                            </div>
                            
                            <span class="text-slate-400 font-semibold text-sm">doit payer à</span>                            
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($settlement['receiver']->name, 0, 2)) }}
                                </div>
                                <span class="font-semibold">{{ $settlement['receiver']->name }}</span>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-2xl font-bold text-emerald-600">{{ number_format($settlement['amount'], 2) }} DH</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-bold text-blue-900 mb-1">Comment ça marche ?</h4>
                <p class="text-sm text-blue-800">Les règlements sont calculés automatiquement en fonction des dépenses. Chaque membre doit payer sa part équitable des dépenses totales. Le système optimise les paiements pour minimiser le nombre de transactions entre les membres.</p>
            </div>
        </div>
    </div>
</div>
@endsection
