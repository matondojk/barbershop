<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Barbearia Barcelona | Agendamento</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:300,400,500,600,700,800|playfair-display:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Tipografia Customizada para um toque mais sofisticado */
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
        body, p, a, button, .font-sans, input, select { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-zinc-300 min-h-screen font-sans selection:bg-barber-primary selection:text-black antialiased relative">
    
    <!-- Background Full Screen Image -->
    <div class="fixed inset-0 w-full h-full z-0">
        <div class="absolute inset-0 bg-black/70 z-10"></div>
        <img src="https://images.unsplash.com/photo-1593702295094-aea22597af65?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Background" class="w-full h-full object-cover">
    </div>

    <!-- Main Container -->
    <div class="relative z-20 min-h-screen flex flex-col justify-center items-center p-4 py-12">
        
        <div class="w-full max-w-2xl bg-[#111]/80 backdrop-blur-xl border border-zinc-800/50 p-6 md:p-10 rounded-[2rem] shadow-2xl relative overflow-hidden">
            <!-- Efeito luminoso no fundo do bloco -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-barber-primary/5 rounded-full blur-[100px] pointer-events-none"></div>

            <!-- Logo e Voltar -->
            <div class="flex flex-col items-center justify-center mb-8 gap-4">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-16 w-auto object-contain drop-shadow-[0_0_15px_rgba(237,179,41,0.2)]">
                </a>
                <div class="text-center">
                    <h2 class="text-3xl text-white font-serif mb-1">Agende seu Horário</h2>
                    <p class="text-zinc-400 text-sm">Rápido, prático e sem filas.</p>
                </div>
            </div>

            <!-- Formulário Livewire -->
            <div class="relative z-10">
                <livewire:scheduling-wizard />
            </div>
            
            <!-- Voltar para o site -->
            <div class="mt-8 text-center border-t border-zinc-800/50 pt-6">
                <a href="/" class="text-zinc-500 hover:text-barber-primary text-sm font-medium transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Voltar para o site inicial
                </a>
            </div>
        </div>
        
    </div>

    @livewireScripts
</body>
</html>
