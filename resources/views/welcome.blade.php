<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyColoc - La gestion de colocation simplifiée</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="bg-slate-50 overflow-x-hidden">
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-800">EasyColoc</span>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-slate-700 font-medium hover:text-indigo-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-slate-700 font-medium hover:text-indigo-600">Connexion</a>
                        <a href="{{ route('register') }}" class="btn-primary">S'inscrire</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <header class="pt-32 pb-16 md:pt-48 md:pb-32 px-4 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
            <div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-indigo-100 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-emerald-100 rounded-full blur-3xl opacity-50"></div>
        </div>

        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 tracking-tight mb-6">
                Finis les calculs, <br>
                <span class="text-indigo-600">vivez votre colocation.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Gérez vos dépenses communes, répartissez les dettes automatiquement et gardez une vision claire de qui doit quoi à qui.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="btn-primary h-14 px-8 text-lg min-w-[200px]">Commencer gratuitement</a>
            </div>
        </div>
    </header>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-20">
                <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">Tout ce qu'il vous faut</h2>
                <p class="text-slate-500 text-lg">Une gestion simple, transparente et automatisée.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-indigo-100 transition-all border border-transparent hover:border-indigo-100">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Calcul Automatique</h3>
                    <p class="text-slate-600 leading-relaxed">Fini Excel. L'application calcule automatiquement les soldes et simplifie les remboursements entre membres.</p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-emerald-100 transition-all border border-transparent hover:border-emerald-100">
                    <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Système de Réputation</h3>
                    <p class="text-slate-600 leading-relaxed">Valorisez les bons payeurs. Un score de réputation permet de suivre le sérieux financier de chaque membre.</p>
                </div>

                <div class="p-8 rounded-2xl bg-slate-50 hover:bg-white hover:shadow-xl hover:shadow-rose-100 transition-all border border-transparent hover:border-rose-100">
                    <div class="w-14 h-14 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi-Gestion</h3>
                    <p class="text-slate-600 leading-relaxed">Invitez vos colocataires en un clic via un lien sécurisé. Gérez plusieurs colocations sans effort.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-indigo-900">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-8">Prêt à simplifier votre vie en colocation ?</h2>
            <p class="text-indigo-200 text-lg mb-12">Rejoignez des centaines d'utilisateurs qui font confiance à EasyColoc pour leurs finances communes.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('register') }}" class="bg-white text-indigo-900 h-14 px-8 py-4 rounded-xl font-bold hover:bg-slate-100 transition-all">S'inscrire maintenant</a>
                <a href="{{ route('login') }}" class="text-white h-14 px-8 py-4 rounded-xl font-bold border border-indigo-700 hover:bg-indigo-800 transition-all">Consulter mon compte</a>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 md:flex justify-between items-center">
            <div class="flex items-center gap-2 mb-8 md:mb-0">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white">EasyColoc</span>
            </div>
            <p class="text-slate-500 text-sm">© 2026 EasyColoc. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
