<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>Barbearia Barcelona | Área do Cliente</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=open-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Open Sans', sans-serif; }
    </style>
</head>
<body class="bg-black text-zinc-300 min-h-screen font-sans selection:bg-barber-primary selection:text-black antialiased relative">
    
    <!-- Background Full Screen Image -->
    <div class="fixed inset-0 w-full h-full z-0">
        <div class="absolute inset-0 bg-black/60 z-10"></div>
        <img src="{{ asset('images/bg/login-bg-1.jpg') }}" alt="Background" class="w-full h-full object-cover">
    </div>

    <!-- Login Container -->
    <div class="relative z-20 min-h-screen flex flex-col justify-center items-center p-4">
        
        <!-- Glassmorphism Card -->
        <div class="w-full max-w-sm sm:max-w-md bg-[#0a0a0a]/70 backdrop-blur-xl border border-zinc-800/50 p-8 sm:p-10 rounded-[2rem] shadow-2xl">
            
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-16 w-auto object-contain drop-shadow-[0_0_15px_rgba(237,179,41,0.2)]">
                </a>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl text-white font-bold tracking-tight mb-1">Bem-vindo(a)</h2>
                <p class="text-zinc-400 text-sm font-light">Faça login para agendar seu horário</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm font-medium text-center backdrop-blur-md">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- CPF Field -->
                <div>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 text-zinc-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required autofocus
                            placeholder="Seu CPF"
                            maxlength="14"
                            class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-2xl pl-12 pr-4 py-3.5 text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium"
                            oninput="maskCPF(this)">
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 text-zinc-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            placeholder="Senha (6 primeiros dígitos)"
                            class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-2xl pl-12 pr-4 py-3.5 text-white placeholder-zinc-500 focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-1 pb-2">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative flex items-center justify-center">
                            <input type="checkbox" name="remember" class="peer sr-only">
                            <div class="w-5 h-5 rounded border border-zinc-600 bg-zinc-900/50 peer-checked:bg-barber-primary peer-checked:border-barber-primary transition-all"></div>
                            <svg class="absolute w-3 h-3 text-black opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm text-zinc-400 group-hover:text-zinc-200 transition-colors">Lembrar de mim</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 bg-barber-primary hover:bg-barber-light text-black text-sm font-extrabold uppercase tracking-widest rounded-2xl transition-all shadow-[0_0_15px_rgba(237,179,41,0.15)] hover:shadow-[0_0_25px_rgba(237,179,41,0.3)] active:scale-95">
                    Entrar
                </button>
            </form>
            
        </div>
        
    </div>

    <script>
        function maskCPF(input) {
            let v = input.value.replace(/\D/g, "");
            
            if (v.length > 11) v = v.substring(0, 11);
            
            if (v.length > 9) {
                v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
            } else if (v.length > 6) {
                v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
            } else if (v.length > 3) {
                v = v.replace(/(\d{3})(\d{1,3})/, "$1.$2");
            }
            
            input.value = v;
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const cpfInput = document.getElementById('cpf');
            if (cpfInput && cpfInput.value) maskCPF(cpfInput);
        });
    </script>
</body>
</html>
