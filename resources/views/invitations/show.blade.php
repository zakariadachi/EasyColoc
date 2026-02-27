<!DOCTYPE html>
<html>
<head>
    <title>Colocation Invitation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Invitation à rejoindre</h1>
            <p class="text-3xl font-bold text-indigo-600">{{ $invitation->colocation->name }}</p>
        </div>

        @if (Auth::check())
            <div class="space-y-3">
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                        Accepter l'invitation
                    </button>
                </form>
                <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}">
                    @csrf
                    <button type="submit" class="w-full px-6 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold transition">
                        Refuser
                    </button>
                </form>
            </div>
        @else
            <div class="text-center">
                <p class="text-slate-600 mb-4">Veuillez vous connecter ou créer un compte pour accepter cette invitation</p>
                <div class="space-y-3">
                    <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
                        Se connecter
                    </a>
                    <a href="{{ route('register') }}" class="block w-full px-6 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold transition">
                        Créer un compte
                    </a>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
