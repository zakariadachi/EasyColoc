@extends('layouts.dashboard')

@section('page-title', 'Mon Profil')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Reputation Card -->
    <div class="bg-white rounded-2xl border border-slate-100 p-8 flex flex-col md:flex-row items-center gap-8 shadow-sm">
        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center text-4xl font-extrabold shadow-lg">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="text-center md:text-left flex-1">
            <h3 class="text-2xl font-bold text-slate-900">{{ auth()->user()->name }}</h3>
            <p class="text-slate-500 mb-4">{{ auth()->user()->email }}</p>
            <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                <div class="bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">
                    <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest">Ma Réputation</p>
                    <p class="text-xl font-black text-emerald-600">+{{ auth()->user()->reputation ?? 0 }}</p>
                </div>
                <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100">
                    <p class="text-[10px] font-bold text-indigo-800 uppercase tracking-widest">Membre depuis</p>
                    <p class="text-xl font-black text-indigo-600">{{ auth()->user()->created_at->format('M. Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
        <h4 class="text-lg font-bold mb-6">Informations personnelles</h4>
        @include('profile.partials.update-profile-information-form')
    </div>

    <!-- Password Change -->
    <div class="bg-white rounded-2xl border border-rose-100 p-8 shadow-sm">
        <h4 class="text-lg font-bold mb-6 text-rose-900">Sécurité</h4>
        @include('profile.partials.update-password-form')
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
        @include('profile.partials.delete-user-form')
    </div>
</div>
@endsection
