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
    <div x-data="{ mobileMenuOpen: false, sidebarExpanded: true, fullscreen: false }" 
         @toggle-fullscreen.window="fullscreen = !fullscreen"
         class="relative z-20 min-h-screen flex w-full">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             x-transition.opacity 
             class="fixed inset-0 bg-black/60 z-40 lg:hidden backdrop-blur-sm" style="display: none;"></div>

        <!-- Sidebar -->
        <aside x-show="!fullscreen" 
               :class="mobileMenuOpen ? 'translate-x-0 w-72' : '-translate-x-full lg:translate-x-0 ' + (sidebarExpanded ? 'w-72' : 'w-20')"
               class="fixed lg:static inset-y-0 left-0 z-50 bg-[#0a0a0a]/60 backdrop-blur-xl flex flex-col transition-all duration-300 ease-in-out">
            
            <!-- Logo area -->
            <div class="h-20 flex items-center justify-between transition-all duration-300" :class="sidebarExpanded ? 'px-5' : 'px-0 justify-center'">
                <a href="/" x-show="sidebarExpanded" class="flex-1 flex justify-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-10 w-auto object-contain drop-shadow-[0_0_10px_rgba(237,179,41,0.2)]">
                </a>
                <button @click="sidebarExpanded = !sidebarExpanded" class="hidden lg:flex text-zinc-400 hover:text-white p-2 rounded-lg hover:bg-white/5 transition-colors">
                    <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    <svg x-show="!sidebarExpanded" style="display: none;" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <button @click="mobileMenuOpen = false" class="lg:hidden text-zinc-400 hover:text-white" x-show="sidebarExpanded">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 py-6 px-3 space-y-6 overflow-y-auto custom-scrollbar overflow-x-hidden">
                <!-- Principal -->
                <div class="space-y-1">
                    <p x-show="sidebarExpanded" class="px-3 text-[11px] font-bold uppercase tracking-wider text-zinc-500 mb-2 flex items-center gap-2 transition-opacity duration-300">
                        <span class="w-1 h-1 rounded-full bg-barber-primary/50"></span>
                        Principal
                    </p>
                    <a wire:navigate href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Agenda do Dia">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Agenda do Dia</span>
                    </a>

                    @if(in_array('barber', auth()->user()->roles ?? []))
                        <a wire:navigate href="{{ route('agenda.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('agenda.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Minha Agenda">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('agenda.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Minha Agenda</span>
                        </a>
                    @endif
                </div>
                
                @if(auth()->user()->isAdmin())
                    <!-- Movimento -->
                    <div class="space-y-1">
                        <p x-show="sidebarExpanded" class="px-3 text-[11px] font-bold uppercase tracking-wider text-zinc-500 mb-2 flex items-center gap-2 transition-opacity duration-300">
                            <span class="w-1 h-1 rounded-full bg-barber-primary/50"></span>
                            Movimento
                        </p>
                        <a wire:navigate href="{{ route('cashier.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('cashier.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Frente de Caixa">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('cashier.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Frente de Caixa</span>
                        </a>
                        
                        <a wire:navigate href="{{ route('financial.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('financial.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Financeiro">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('financial.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Financeiro</span>
                        </a>
                    </div>

                    <!-- Catálogos -->
                    <div class="space-y-1">
                        <p x-show="sidebarExpanded" class="px-3 text-[11px] font-bold uppercase tracking-wider text-zinc-500 mb-2 flex items-center gap-2 transition-opacity duration-300">
                            <span class="w-1 h-1 rounded-full bg-barber-primary/50"></span>
                            Catálogos
                        </p>
                        <a wire:navigate href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('services.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Serviços">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('services.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Serviços</span>
                        </a>

                        <a wire:navigate href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('products.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Produtos">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('products.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Produtos</span>
                        </a>
                    </div>

                    <!-- Pessoas -->
                    <div class="space-y-1">
                        <p x-show="sidebarExpanded" class="px-3 text-[11px] font-bold uppercase tracking-wider text-zinc-500 mb-2 flex items-center gap-2 transition-opacity duration-300">
                            <span class="w-1 h-1 rounded-full bg-barber-primary/50"></span>
                            Pessoas
                        </p>
                        <a wire:navigate href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('customers.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Clientes">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('customers.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Clientes</span>
                        </a>
                        
                        <a wire:navigate href="{{ route('collaborators.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 hover:text-white transition-all duration-300 {{ request()->routeIs('collaborators.*') ? 'bg-gradient-to-r from-barber-primary/10 to-transparent text-barber-primary border-l-2 border-barber-primary shadow-[inset_0_0_20px_rgba(237,179,41,0.05)]' : 'text-zinc-400 text-sm' }}" :class="!sidebarExpanded ? 'justify-center px-0' : ''" title="Equipe">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('collaborators.*') ? 'text-barber-primary' : 'text-zinc-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="sidebarExpanded" class="font-medium truncate transition-opacity duration-300">Equipe</span>
                        </a>
                    </div>
                @endif
            </nav>

            <div class="p-4 border-t border-zinc-800/50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-3 py-3 w-full rounded-xl text-zinc-400 font-medium hover:text-red-400 hover:bg-red-500/10 transition-colors" :class="!sidebarExpanded ? 'justify-center' : ''" title="Sair">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-show="sidebarExpanded" class="transition-opacity duration-300">Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-h-screen w-full lg:w-auto relative">
            
            <!-- Topbar -->
            <header x-show="!fullscreen" class="h-20 bg-[#0a0a0a]/60 backdrop-blur-xl border-b border-zinc-800/50 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 -ml-2 text-zinc-300 hover:text-white rounded-lg hover:bg-white/5">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
                
                <div class="flex items-center gap-4 lg:gap-6">
                    <!-- Notification Bell -->
                    <livewire:notification-bell />

                    <!-- Profile Dropdown -->
                    <div x-data="{ open: false }" class="relative z-50">
                        <button @click="open = true" class="flex items-center gap-3 lg:gap-4 hover:opacity-80 transition-opacity focus:outline-none">
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

                        <!-- Mobile Overlay -->
                        <div x-show="open" @click="open = false" x-transition.opacity class="fixed inset-0 bg-black/60 z-[60] lg:hidden backdrop-blur-sm" style="display: none;"></div>

                        <!-- Dropdown Menu / Mobile Sidebar -->
                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-300 lg:duration-150"
                             x-transition:enter-start="translate-x-full lg:translate-x-0 lg:opacity-0 lg:scale-95"
                             x-transition:enter-end="translate-x-0 lg:opacity-100 lg:scale-100"
                             x-transition:leave="transition ease-in duration-300 lg:duration-100"
                             x-transition:leave-start="translate-x-0 lg:opacity-100 lg:scale-100"
                             x-transition:leave-end="translate-x-full lg:translate-x-0 lg:opacity-0 lg:scale-95"
                             style="display: none;" 
                             class="fixed inset-y-0 right-0 w-[85%] max-w-sm bg-black/90 backdrop-blur-2xl border-l border-zinc-800 shadow-2xl z-[70] flex flex-col lg:absolute lg:inset-auto lg:right-0 lg:mt-3 lg:w-56 lg:bg-black/90 lg:rounded-2xl lg:border lg:flex-none py-0 lg:py-2">
                            
                            <!-- Mobile Header -->
                            <div class="lg:hidden p-5 border-b border-zinc-800/50 flex items-center justify-between">
                                <h3 class="text-white font-medium text-lg">Meu Perfil</h3>
                                <button @click="open = false" class="text-zinc-400 hover:text-white">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="px-5 py-6 border-b border-zinc-800/50 mb-2 lg:hidden">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-full bg-zinc-900 border-2 border-barber-primary/50 flex items-center justify-center text-barber-primary font-bold overflow-hidden shadow-[0_0_10px_rgba(237,179,41,0.2)]">
                                        @if(auth()->user()->photo_path)
                                            <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm text-zinc-500 mb-1">Logado como</p>
                                        <p class="text-lg font-bold text-white truncate leading-none">{{ auth()->user()->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Header -->
                            <div class="px-4 py-3 border-b border-zinc-800/50 mb-1 hidden lg:block">
                                <p class="text-xs text-zinc-500">Logado como</p>
                                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            </div>
                            
                            <div class="flex-1 lg:flex-none">
                                <a wire:navigate href="{{ route('profile.edit') }}" class="flex items-center gap-4 lg:gap-3 px-6 lg:px-4 py-4 lg:py-3 text-base lg:text-sm font-medium text-zinc-400 hover:text-white hover:bg-white/5 transition-colors border-b border-zinc-800/50 lg:border-none">
                                    <svg class="w-5 h-5 lg:w-4 lg:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Editar Perfil
                                </a>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="block lg:border-t lg:border-zinc-800/50 lg:mt-1 lg:pt-1 mt-auto lg:mt-0 mb-6 lg:mb-0">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-4 lg:gap-3 px-6 lg:px-4 py-4 lg:py-3 text-left text-base lg:text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                                    <svg class="w-5 h-5 lg:w-4 lg:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair da Conta
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 p-4 lg:p-8 pb-24 lg:pb-8 overflow-x-hidden overflow-y-auto">
                @if(session('success'))
                    <div class="bg-green-500/10 backdrop-blur-md border border-green-500/30 text-green-400 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 font-medium shadow-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>

            <!-- Mobile Bottom Tab Navigation -->
            <nav x-show="!fullscreen" class="lg:hidden fixed bottom-0 w-full bg-[#0a0a0a]/90 backdrop-blur-xl border-t border-zinc-800/80 z-40 pb-2">
                <div class="flex items-center justify-around h-16 px-1">
                    <a wire:navigate href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center justify-center h-full {{ request()->routeIs('dashboard') ? 'text-barber-primary' : 'text-zinc-500 hover:text-zinc-300' }}">
                        <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-[9px] font-medium uppercase tracking-wider">Início</span>
                    </a>

                    @if(in_array('barber', auth()->user()->roles ?? []))
                        <a wire:navigate href="{{ route('agenda.index') }}" class="flex-1 flex flex-col items-center justify-center h-full {{ request()->routeIs('agenda.*') ? 'text-barber-primary' : 'text-zinc-500 hover:text-zinc-300' }}">
                            <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[9px] font-medium uppercase tracking-wider text-center leading-tight">Agenda</span>
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a wire:navigate href="{{ route('cashier.index') }}" class="flex-1 flex flex-col items-center justify-center h-full {{ request()->routeIs('cashier.*') ? 'text-barber-primary' : 'text-zinc-500 hover:text-zinc-300' }}">
                            <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-[9px] font-medium uppercase tracking-wider">Caixa</span>
                        </a>
                        <a wire:navigate href="{{ route('customers.index') }}" class="flex-1 flex flex-col items-center justify-center h-full {{ request()->routeIs('customers.*') ? 'text-barber-primary' : 'text-zinc-500 hover:text-zinc-300' }}">
                            <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="text-[9px] font-medium uppercase tracking-wider">Clientes</span>
                        </a>
                        <a wire:navigate href="{{ route('services.index') }}" class="flex-1 flex flex-col items-center justify-center h-full {{ request()->routeIs('services.*') ? 'text-barber-primary' : 'text-zinc-500 hover:text-zinc-300' }}">
                            <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                            <span class="text-[9px] font-medium uppercase tracking-wider">Serviços</span>
                        </a>
                    @endif
                    
                    <button @click="mobileMenuOpen = true" class="flex-1 flex flex-col items-center justify-center h-full text-zinc-500 hover:text-zinc-300">
                        <svg class="w-6 h-6 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span class="text-[9px] font-medium uppercase tracking-wider">Mais</span>
                    </button>
                </div>
            </nav>
        </main>
    </div>

    @livewireScripts
</body>
</html>
