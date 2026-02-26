<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - EasyColoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-slate-50 h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 mb-4">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Bon retour !</h1>
            <p class="text-slate-500 mt-2">Prêt à gérer vos dépenses ?</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Adresse email</label>
                    <input type="email" id="email" name="email" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="exemple@mail.com" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Mot de passe</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Oublié ?</a>
                        @endif
                    </div>
                    <input type="password" id="password" name="password" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-11" placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-slate-600">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-primary w-full h-11 text-base shadow-lg shadow-indigo-200">Se connecter</button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Pas encore de compte ? 
                    <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-700">Créer une colocation</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
