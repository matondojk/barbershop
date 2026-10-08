<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Dashboard | Barbearia Barcelona</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=open-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body, h1, h2, h3, .font-serif, .font-sans { font-family: 'Open Sans', sans-serif !important; }
    </style>
</head>
<body class="bg-black text-zinc-300 font-sans antialiased min-h-screen relative selection:bg-barber-primary selection:text-black">
    
    <!-- Background Full Screen Image -->
    <div class="fixed inset-0 w-full h-full z-0">
        <div class="absolute inset-0 bg-black/70 z-10"></div>
        <img src="{{ asset('images/bg/login-bg-1.jpg') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    <!-- Application Wrapper -->
    <div x-data="{ mobileMenuOpen: false }" class="relative z-20 min-h-screen flex w-full">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             x-transition.opacity 
             class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-[#0a0a0a]/80 backdrop-blur-2xl border-r border-zinc-800/50 flex flex-col transition-transform duration-300 ease-in-out">
            
            <!-- Logo area -->
            <div class="h-20 flex items-center justify-between lg:justify-center px-6 lg:px-0 border-b border-zinc-800/50">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-10 w-auto object-contain drop-shadow-[0_0_10px_rgba(237,179,41,0.2)]">
                </a>
                <button @click="mobileMenuOpen = false" class="lg:hidden text-zinc-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 py-6 px-4 space-y-2 overflow-y-auto custom-scrollbar">
                <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-white/5 hover:text-barber-primary transition-colors {{ request()->routeIs('dashboard') ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20 shadow-[inset_0_0_10px_rgba(237,179,41,0.1)]' : 'text-zinc-400 font-medium' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Visão Geral
                </a>

                @if(in_array('barber', auth()->user()->roles ?? []))
                    <a wire:navigate href="{{ route('agenda.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-white/5 hover:text-barber-primary transition-colors {{ request()->routeIs('agenda.*') ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20 shadow-[inset_0_0_10px_rgba(237,179,41,0.1)]' : 'text-zinc-400 font-medium' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Minha Agenda
                    </a>
                @endif
                
                @if(auth()->user()->isAdmin())
                    <a wire:navigate href="{{ route('cashier.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-white/5 hover:text-barber-primary transition-colors {{ request()->routeIs('cashier.*') ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20 shadow-[inset_0_0_10px_rgba(237,179,41,0.1)]' : 'text-zinc-400 font-medium' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Frente de Caixa
                    </a>
                    <a wire:navigate href="{{ route('collaborators.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-white/5 hover:text-barber-primary transition-colors {{ request()->routeIs('collaborators.*') ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20 shadow-[inset_0_0_10px_rgba(237,179,41,0.1)]' : 'text-zinc-400 font-medium' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Equipe
                    </a>

                    <a wire:navigate href="{{ route('services.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-xl hover:bg-white/5 hover:text-barber-primary transition-colors {{ request()->routeIs('services.*') ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20 shadow-[inset_0_0_10px_rgba(237,179,41,0.1)]' : 'text-zinc-400 font-medium' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        Cortes
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-zinc-800/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-3.5 w-full rounded-xl text-zinc-400 font-medium hover:text-red-400 hover:bg-red-500/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen w-full lg:w-auto relative">
            
            <!-- Topbar -->
            <header class="h-20 bg-[#0a0a0a]/60 backdrop-blur-xl border-b border-zinc-800/50 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 -ml-2 text-zinc-300 hover:text-white rounded-lg hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
                
                <div class="flex items-center gap-4 lg:gap-6">
                    <!-- Notification Bell -->
                    <livewire:notification-bell />

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-3 lg:gap-4 hover:opacity-80 transition-opacity focus:outline-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-widest">{{ in_array('barber', auth()->user()->roles ?? []) ? 'Barbeiro' : 'Cliente' }}</p>
                            </div>
                            <div class="w-10 h-10 lg:w-11 lg:h-11 rounded-full bg-zinc-900 border-2 border-barber-primary/50 flex items-center justify-center text-barber-primary font-bold overflow-hidden shadow-[0_0_10px_rgba(237,179,41,0.2)]">
                                @if(auth()->user()->photo_path)
                                    <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                @endif
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.outside="open = false" x-transition style="display: none;" class="absolute right-0 mt-3 w-56 bg-[#0a0a0a]/90 backdrop-blur-2xl border border-zinc-800/50 rounded-2xl shadow-2xl z-50 overflow-hidden py-2">
                            <div class="px-4 py-3 border-b border-zinc-800/50 mb-1 sm:hidden">
                                <p class="text-xs text-zinc-500">Logado como</p>
                                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-zinc-400 hover:text-white hover:bg-white/5 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Editar Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block border-t border-zinc-800/50 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair da Conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-4 lg:p-8 overflow-x-hidden overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-500/10 backdrop-blur-md border border-green-500/30 text-green-400 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 font-medium shadow-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
