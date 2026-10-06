<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barbearia Barcelona | Área do Cliente</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600|playfair-display:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
        body, p, a, button, input, .font-sans { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="bg-[#050505] text-zinc-300 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-10">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-16 w-auto object-contain hover:scale-105 transition-transform duration-300">
            </a>
        </div>

        <!-- Login Box -->
        <div class="bg-[#111] p-10 rounded-2xl border border-zinc-800 shadow-2xl relative overflow-hidden">
            <!-- Glow effect -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-barber-primary/10 rounded-full blur-3xl"></div>

            <div class="text-center mb-8 relative z-10">
                <h2 class="text-3xl text-white font-serif mb-2">Acesse sua Conta</h2>
                <p class="text-zinc-500 text-sm">Entre para agendar seu horário</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                @csrf

                <div>
                    <label for="cpf" class="block text-sm font-medium text-zinc-400 mb-2">CPF</label>
                    <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required autofocus
                        placeholder="000.000.000-00"
                        maxlength="14"
                        class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors placeholder-zinc-700"
                        oninput="maskCPF(this)">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-400 mb-2">Senha (6 primeiros dígitos do CPF)</label>
                    <input id="password" type="password" name="password" required
                        class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-zinc-800 bg-[#0a0a0a] text-barber-primary focus:ring-barber-primary focus:ring-offset-[#111]">
                        <span class="text-sm text-zinc-400">Lembrar de mim</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-4 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_20px_rgba(237,179,41,0.4)]">
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
        
        // Formatar no load caso haja um "old('cpf')" populado
        document.addEventListener('DOMContentLoaded', () => {
            const cpfInput = document.getElementById('cpf');
            if (cpfInput && cpfInput.value) maskCPF(cpfInput);
        });
    </script>
</body>
</html>
