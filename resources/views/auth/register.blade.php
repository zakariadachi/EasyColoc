<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - EasyColoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full py-8">
        <div class="text-center mb-8">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 mb-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Créer un compte</h1>
            <p class="text-slate-500 mt-2">Rejoignez l'aventure EasyColoc</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Nom complet</label>
                    <input type="text" id="name" name="name" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="Alice Martin" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Adresse email</label>
                    <input type="email" id="email" name="email" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="exemple@mail.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="••••••••">
                    <p class="mt-1 text-xs text-slate-400">Minimum 8 caractères</p>
                    @error('password')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-primary w-full h-11 text-base shadow-lg shadow-indigo-200 mt-4">Créer mon compte</button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Déjà inscrit ? 
                    <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
