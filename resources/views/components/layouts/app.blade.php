<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Barbearia Barcelona</title>
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700|playfair-display:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
        body, p, a, button, input, select, .font-sans { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#050505] text-zinc-300 font-sans antialiased min-h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-[#0a0a0a] border-r border-zinc-800 flex flex-col hidden md:flex">
        <div class="h-20 flex items-center justify-center border-b border-zinc-800">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-10 w-auto object-contain">
            </a>
        </div>
        
        <nav class="flex-1 py-6 px-4 space-y-2">
            <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#111] hover:text-barber-primary transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#111] text-barber-primary border border-zinc-800' : 'text-zinc-400' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Visão Geral
            </a>

            @if(in_array('barber', auth()->user()->roles ?? []))
                <a wire:navigate href="{{ route('agenda.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#111] hover:text-barber-primary transition-colors {{ request()->routeIs('agenda.*') ? 'bg-[#111] text-barber-primary border border-zinc-800' : 'text-zinc-400' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Minha Agenda
                </a>
            @endif
            
            @if(auth()->user()->isAdmin())
                <a wire:navigate href="{{ route('cashier.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#111] hover:text-barber-primary transition-colors {{ request()->routeIs('cashier.*') ? 'bg-[#111] text-barber-primary border border-zinc-800' : 'text-zinc-400' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Frente de Caixa
                </a>
                <a wire:navigate href="{{ route('collaborators.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#111] hover:text-barber-primary transition-colors {{ request()->routeIs('collaborators.*') ? 'bg-[#111] text-barber-primary border border-zinc-800' : 'text-zinc-400' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Equipe
                </a>

                <a wire:navigate href="{{ route('services.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-[#111] hover:text-barber-primary transition-colors {{ request()->routeIs('services.*') ? 'bg-[#111] text-barber-primary border border-zinc-800' : 'text-zinc-400' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                    Cortes
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-zinc-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 w-full rounded-lg text-zinc-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-h-screen">
        <!-- Topbar -->
        <header class="h-20 bg-[#0a0a0a] border-b border-zinc-800 flex items-center justify-between px-8">
            <h1 class="text-xl text-white font-serif tracking-wide">@yield('header_title', 'Dashboard')</h1>
            
            <div class="flex items-center gap-6">
                <!-- Notification Bell -->
                <livewire:notification-bell />

                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-4 hover:opacity-80 transition-opacity focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-zinc-800 border border-barber-primary/50 flex items-center justify-center text-barber-primary font-bold overflow-hidden">
                            @if(auth()->user()->photo_path)
                                <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.outside="open = false" x-transition style="display: none;" class="absolute right-0 mt-3 w-56 bg-[#111] border border-zinc-800 rounded-xl shadow-2xl z-50 overflow-hidden py-2">
                        <div class="px-4 py-3 border-b border-zinc-800 mb-1">
                            <p class="text-xs text-zinc-500">Logado como</p>
                            <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        </div>
                        <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-zinc-400 hover:text-white hover:bg-[#1a1a1a] transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Editar Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block border-t border-zinc-800 mt-1 pt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-red-500 hover:bg-red-500/10 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sair da Conta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/30 text-green-400 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    @livewireScripts
</body>
</html>
