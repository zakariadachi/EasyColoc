@extends('layouts.dashboard')

@section('page-title', 'Mes Colocations')

@section('header-actions')
    <button data-modal-target="createModal" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Créer
    </button>
@endsection

@section('content')
@if($colocations->isEmpty())
    <div class="text-center py-16">
        <h3 class="text-xl font-bold text-slate-900 mb-2">Aucune colocation</h3>
        <p class="text-slate-500 mb-6">Créez votre première colocation</p>
        <button data-modal-target="createModal" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Créer</button>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($colocations as $colocation)
            <a href="{{ route('colocations.show', $colocation) }}" class="bg-white p-6 rounded-xl border border-slate-200 hover:shadow-lg transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $colocation->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $colocation->address }}</p>
                    </div>
                    @if($colocation->owner_id === auth()->id())
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">Owner</span>
                    @endif
                </div>
                <p class="text-sm text-slate-600">{{ $colocation->members->count() }} membres</p>
            </a>
        @endforeach
    </div>
@endif

<div id="createModal" class="modal-container fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60">
    <div class="bg-white w-full max-w-md rounded-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Créer une colocation</h3>
            <button data-modal-close class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('colocations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nom</label>
                <input type="text" name="name" class="w-full px-4 py-2 border border-slate-300 rounded-lg" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Adresse</label>
                <input type="text" name="address" class="w-full px-4 py-2 border border-slate-300 rounded-lg" required>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Créer</button>
        </form>
    </div>
</div>
@endsection
