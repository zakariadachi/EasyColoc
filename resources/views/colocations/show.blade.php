@extends('layouts.dashboard')

@section('page-title', $colocation->name)

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-8 rounded-xl">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $colocation->name }}</h1>
                <p class="text-indigo-100">{{ $colocation->address }}</p>
            </div>
            <div class="flex gap-3">
                <button data-modal-target="inviteModal" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg">Inviter</button>
                @if($colocation->owner_id === auth()->id())
                    <button class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg">Paramètres</button>
                @else
                    <form action="{{ route('colocations.leave', $colocation) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 rounded-lg" onclick="return confirm('Quitter ?')">Quitter</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-bold mb-4">Membres ({{ $colocation->members->count() }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($colocation->members as $member)
                <div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center justify-between group relative">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($member->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-bold">{{ $member->name }}</p>
                            <p class="text-xs text-slate-400">{{ $member->id === $colocation->owner_id ? 'Owner' : 'Membre' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Score</p>
                        <span class="text-sm font-bold text-emerald-600">+{{ $member->reputation ?? 0 }}</span>
                    </div>
                    @if($colocation->owner_id === auth()->id() && $member->id !== auth()->id())
                        <form action="{{ route('colocations.removeMember', [$colocation, $member]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-rose-500 hover:bg-rose-50 rounded" onclick="return confirm('Retirer ?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<div id="inviteModal" class="modal-container fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60">
    <div class="bg-white w-full max-w-md rounded-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Inviter un membre</h3>
            <button data-modal-close class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('colocations.invite', $colocation) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-2 border border-slate-300 rounded-lg" required>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Envoyer</button>
        </form>
    </div>
</div>
@endsection
