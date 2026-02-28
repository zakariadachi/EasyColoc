@extends('layouts.dashboard')

@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-8 rounded-xl">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">Gestion des Utilisateurs</h1>
                <p class="text-purple-100">Liste de tous les utilisateurs de la plateforme</p>
            </div>
            <a href="{{ route('admin.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg">
                Retour
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Réputation</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm">{{ $user->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-semibold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $user->reputation >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $user->reputation >= 0 ? '+' : '' }}{{ $user->reputation }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_admin)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">Admin</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_banned)
                                <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-semibold">Banni</span>
                            @else
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">Actif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$user->is_admin)
                                @if($user->is_banned)
                                    <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm">
                                            Débannir
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm" onclick="return confirm('Bannir cet utilisateur ?')">
                                            Bannir
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
