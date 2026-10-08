<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barbearia Barcelona | Excelência & Estilo</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:300,400,500,600,700,800|playfair-display:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Tipografia Customizada para um toque mais sofisticado */
        h1, h2, h3, .font-serif { font-family: 'Playfair Display', serif; }
        body, p, a, button, .font-sans { font-family: 'Montserrat', sans-serif; }
        
        /* Animações customizadas */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Botão pulsante com brilho dourado */
        .btn-glow {
            box-shadow: 0 0 15px rgba(237, 179, 41, 0.5);
            animation: pulse-glow 2s infinite;
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 15px rgba(237, 179, 41, 0.4); }
            50% { box-shadow: 0 0 25px rgba(237, 179, 41, 0.8); transform: scale(1.02); }
            100% { box-shadow: 0 0 15px rgba(237, 179, 41, 0.4); }
        }

        /* Efeito glassmorphism */
        .glass {
            background: rgba(17, 17, 17, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(237, 179, 41, 0.1);
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-zinc-300 antialiased selection:bg-barber-primary selection:text-black overflow-x-hidden">

    <!-- Header / Navbar -->
    <header id="header" class="fixed w-full top-0 z-50 transition-all duration-300 border-b border-transparent bg-transparent">
        <div class="max-w-7xl mx-auto px-6 md:px-12 h-20 flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="flex items-center group">
                <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-8 w-auto object-contain transition-opacity duration-300 group-hover:opacity-80">
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-10">
                <a href="#home" class="text-sm tracking-[0.1em] text-white/70 hover:text-white transition-colors uppercase font-medium">Início</a>
                <a href="#services" class="text-sm tracking-[0.1em] text-white/70 hover:text-white transition-colors uppercase font-medium">Serviços</a>
                <a href="#about" class="text-sm tracking-[0.1em] text-white/70 hover:text-white transition-colors uppercase font-medium">A Barbearia</a>
                <a href="#gallery" class="text-sm tracking-[0.1em] text-white/70 hover:text-white transition-colors uppercase font-medium">Galeria</a>
            </nav>

            <!-- CTA Desktop -->
            <div class="hidden md:block">
                <a href="{{ route('scheduling') }}" class="px-7 py-2.5 bg-barber-primary text-black text-xs font-bold uppercase tracking-widest rounded-full hover:brightness-110 transition-all shadow-[0_0_10px_rgba(237,179,41,0.2)] hover:shadow-[0_0_15px_rgba(237,179,41,0.4)]">
                    Agendar
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white focus:outline-none">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Nav (Dropdown) -->
        <div id="mobile-menu" class="fixed inset-0 bg-black/95 z-40 hidden flex-col items-center justify-center space-y-8 backdrop-blur-lg">
            <button id="close-menu-btn" class="absolute top-8 right-8 text-white/70 hover:text-white">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <a href="#home" class="mobile-link text-2xl font-serif text-white hover:text-white/70 transition-colors">Início</a>
            <a href="#services" class="mobile-link text-2xl font-serif text-white hover:text-white/70 transition-colors">Serviços</a>
            <a href="#about" class="mobile-link text-2xl font-serif text-white hover:text-white/70 transition-colors">A Barbearia</a>
            <a href="#gallery" class="mobile-link text-2xl font-serif text-white hover:text-white/70 transition-colors">Galeria</a>
            <a href="{{ route('scheduling') }}" class="mobile-link mt-8 px-10 py-4 bg-barber-primary text-black text-sm font-bold uppercase tracking-widest rounded-full hover:brightness-110 transition-all shadow-[0_0_15px_rgba(237,179,41,0.3)]">Agendar Agora</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-start overflow-hidden pt-20">
        <!-- Background elements -->
        <div class="absolute inset-0 z-0 bg-black">
            <img src="{{ asset('images/bb/corte-1.webp') }}" alt="Barbearia Fundo" class="w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent"></div>
        </div>

        <div class="relative z-10 px-6 md:px-12 w-full max-w-7xl mx-auto reveal">
            <div class="max-w-2xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-px bg-barber-primary"></div>
                    <span class="text-barber-primary tracking-[0.2em] text-sm font-medium uppercase">Tradição & Estilo</span>
                </div>
                <h1 class="text-5xl md:text-7xl text-white font-serif mb-6 leading-[1.1]">
                    Onde a excelência encontra a sua imagem.
                </h1>
                <p class="text-lg md:text-xl text-zinc-400 mb-12 font-light leading-relaxed max-w-xl">
                    Mais do que um simples corte de cabelo. Proporcionamos uma experiência de cuidado pessoal focada nos detalhes, conforto e exclusividade.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <a href="{{ route('scheduling') }}" class="w-full sm:w-auto px-8 py-4 bg-barber-primary text-black text-sm font-bold uppercase tracking-widest rounded-full hover:brightness-110 transition-all shadow-[0_0_15px_rgba(237,179,41,0.3)] hover:shadow-[0_0_25px_rgba(237,179,41,0.5)] transform hover:-translate-y-1 text-center">
                        Agendar Horário
                    </a>
                    <a href="#services" class="w-full sm:w-auto px-8 py-4 border border-barber-primary text-barber-primary text-sm font-bold uppercase tracking-widest rounded-full hover:bg-barber-primary hover:text-black transition-all transform hover:-translate-y-1 text-center">
                        Nossos Serviços
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-32 relative bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8 reveal">
                <div>
                    <span class="text-barber-primary tracking-[0.2em] text-sm uppercase block mb-3">O que oferecemos</span>
                    <h2 class="text-4xl md:text-5xl text-white font-serif">Nossos Serviços</h2>
                </div>
                <p class="text-zinc-400 max-w-md text-sm md:text-base font-light">
                    Trabalhamos com produtos premium e profissionais altamente qualificados para garantir o melhor resultado.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12">
                @forelse($services as $index => $service)
                    <div class="group cursor-default reveal" style="transition-delay: {{ ($index % 4) * 50 }}ms;">
                        <div class="flex justify-between items-baseline mb-3">
                            <h3 class="text-xl text-white font-medium group-hover:text-barber-primary transition-colors">
                                {{ $service->name }}
                                <span class="text-xs text-barber-primary/70 ml-2 font-sans tracking-widest">{{ $service->duration_minutes }} MIN</span>
                            </h3>
                            <div class="flex-grow border-b border-dotted border-zinc-700 mx-4 relative top-[-6px]"></div>
                            <span class="text-xl text-white font-serif">R$ {{ number_format($service->price, 0, ',', '.') }}</span>
                        </div>
                        @if($service->description)
                            <p class="text-zinc-500 text-sm leading-relaxed pr-16 font-light">{{ $service->description }}</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-zinc-500 text-center py-10">Nenhum serviço cadastrado no momento.</div>
                @endforelse
            </div>
            
            <div class="mt-20 text-center reveal">
                <a href="{{ route('scheduling') }}" class="inline-flex items-center gap-3 text-white hover:text-barber-primary transition-colors font-medium tracking-widest uppercase text-xs pb-1 border-b border-white hover:border-barber-primary">
                    Ver disponibilidade e agendar
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-32 bg-[#111]/70 backdrop-blur-md relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Imagem -->
                <div class="relative reveal">
                    <div class="absolute inset-0 bg-barber-primary/20 transform rotate-3 rounded-2xl"></div>
                    <img src="{{ asset('images/bb/corte-3.webp') }}" alt="Ambiente Barbearia" class="relative rounded-2xl shadow-2xl transition-all duration-700 w-full h-[600px] object-cover">
                    
                    <!-- Box flutuante -->
                    <div class="absolute -bottom-10 -right-10 bg-[#0a0a0a]/70 backdrop-blur-md border border-barber-primary/30 p-8 rounded-xl shadow-2xl hidden md:block">
                        <div class="flex items-center gap-4">
                            <div class="text-barber-primary">
                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-4xl font-serif text-white">4.9</p>
                                <p class="text-sm text-zinc-400 uppercase tracking-widest">Estrelas no Google</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Texto -->
                <div class="reveal" style="transition-delay: 200ms;">
                    <span class="text-barber-primary tracking-[0.2em] text-sm font-semibold uppercase">Tradição & Modernidade</span>
                    <h2 class="text-4xl md:text-5xl text-white font-serif mt-2 mb-8 leading-tight">Resgatando a essência do <span class="italic text-transparent bg-clip-text bg-gradient-to-r from-barber-primary to-barber-dark">cavalheiro moderno</span>.</h2>
                    
                    <p class="text-zinc-400 mb-6 leading-relaxed">
                        Esqueça a pressa. Na Barbearia Barcelona, cada corte é um ritual. Nós combinamos a maestria das velhas escolas de barbearia europeias com as tendências contemporâneas que ditam a moda atual.
                    </p>
                    <p class="text-zinc-400 mb-10 leading-relaxed">
                        Ao entrar pela nossa porta, você é recebido com uma bebida premium cortesia, música ambiente rigorosamente selecionada e profissionais que dedicam tempo para entender exatamente o que você busca.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-barber-primary/10 flex items-center justify-center flex-shrink-0 text-barber-primary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-white font-medium text-lg">Ambiente Climatizado e Exclusivo</h4>
                                <p class="text-zinc-500 text-sm mt-1">Design sofisticado para seu total conforto.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-barber-primary/10 flex items-center justify-center flex-shrink-0 text-barber-primary">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-white font-medium text-lg">Bar Open para Clientes</h4>
                                <p class="text-zinc-500 text-sm mt-1">Cervejas artesanais, whisky, café expresso ou água.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeria (Estilo Masonry/Grid Dinâmico) -->
    <section id="gallery" class="py-32 bg-[#0a0a0a]/70 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 reveal">
                <div>
                    <span class="text-barber-primary tracking-[0.2em] text-sm font-semibold uppercase">Portfolio</span>
                    <h2 class="text-4xl md:text-5xl text-white font-serif mt-2">Nossas Obras</h2>
                </div>
                <a href="#" class="hidden md:inline-flex text-zinc-400 hover:text-barber-primary transition-colors tracking-widest uppercase text-sm font-medium">Ver Instagram</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <!-- Row 1 -->
                <div class="col-span-2 row-span-2 relative group overflow-hidden rounded-xl reveal">
                    <img src="{{ asset('images/bb/corte-1.webp') }}" alt="Corte 1" class="w-full h-full object-cover aspect-square md:aspect-auto transform transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                        <span class="border border-barber-primary text-barber-primary px-4 md:px-6 py-2 uppercase tracking-widest text-xs md:text-sm rounded-full">Na Régua</span>
                    </div>
                </div>
                
                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 100ms;">
                    <img src="{{ asset('images/bb/corte-2.webp') }}" alt="Corte 2" class="w-full h-full object-cover aspect-square transform transition-transform duration-700 group-hover:scale-105">
                </div>

                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 150ms;">
                    <img src="{{ asset('images/bb/corte-3.webp') }}" alt="Corte 3" class="w-full h-full object-cover aspect-square transform transition-transform duration-700 group-hover:scale-105">
                </div>

                <!-- Row 2 -->
                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 200ms;">
                    <img src="{{ asset('images/bb/corte-4.webp') }}" alt="Corte 4" class="w-full h-full object-cover aspect-square transform transition-transform duration-700 group-hover:scale-105">
                </div>

                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 250ms;">
                    <img src="{{ asset('images/bb/corte-5.webp') }}" alt="Corte 5" class="w-full h-full object-cover aspect-square transform transition-transform duration-700 group-hover:scale-105">
                </div>
                
                <!-- Row 3 -->
                <div class="col-span-2 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 300ms;">
                    <img src="{{ asset('images/bb/corte-6.webp') }}" alt="Corte 6" class="w-full h-[300px] object-cover transform transition-transform duration-700 group-hover:scale-105">
                </div>
                
                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 350ms;">
                    <img src="{{ asset('images/bb/corte-7.webp') }}" alt="Corte 7" class="w-full h-[300px] object-cover transform transition-transform duration-700 group-hover:scale-105">
                </div>
                <div class="col-span-1 relative group overflow-hidden rounded-xl reveal" style="transition-delay: 400ms;">
                    <img src="{{ asset('images/bb/corte-8.webp') }}" alt="Corte 8" class="w-full h-[300px] object-cover transform transition-transform duration-700 group-hover:scale-105">
                </div>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section id="testimonials" class="py-32 relative bg-[#0a0a0a]">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mb-16 reveal">
                <span class="text-barber-primary tracking-[0.2em] text-sm font-semibold uppercase">O que dizem sobre nós</span>
                <h2 class="text-4xl md:text-5xl text-white font-serif mt-2">Avaliações</h2>
                <div class="w-24 h-1 bg-barber-primary mx-auto mt-6"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Avaliação 1 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Uma experiência inexplicável, ótimo atendimento e excelentes profissionais!!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">T</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Thiago Contemplacar</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 2 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 100ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Top demais! Parabéns para o Nagai e Rafael. Muito top!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">E</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Erikin Guedes</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 3 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 200ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Melhor barbearia de Pará de Minas."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">M</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Marcus Vinícius</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 4 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 300ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Lugar muito bom e os cortes são ótimos."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">R</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Rafael Arthur</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 5 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 400ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Muito obrigado pelo profissionalismo de sempre."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">P</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Pedro Henrique</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 6 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800/50 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 500ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 font-light leading-relaxed mb-6">"Melhores profissionais e excelente atendimento."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 rounded-full flex items-center justify-center text-lg font-bold text-white">C</div>
                        <div>
                            <h4 class="text-white text-sm font-semibold">Carlos Augusto</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Agendamento / Contato (Call to Action Final) -->
    <section id="contact" class="py-32 relative bg-[#111]/70 backdrop-blur-md">
        <!-- Linha Superior -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-barber-primary/30 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="glass rounded-3xl p-8 md:p-16 border border-zinc-800 relative overflow-hidden reveal">
                <!-- Efeito luminoso no fundo do bloco -->
                <div class="absolute top-0 right-0 w-96 h-96 bg-barber-primary/10 rounded-full blur-[100px]"></div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 relative z-10">
                    <div>
                        <span class="text-barber-primary tracking-[0.2em] text-sm font-semibold uppercase">Reserve seu momento</span>
                        <h2 class="text-4xl md:text-5xl text-white font-serif mt-2 mb-8">Agende seu<br>Atendimento</h2>
                        
                        <p class="text-zinc-400 mb-10 text-lg">
                            Diga adeus às filas de espera. Trabalhamos exclusivamente com horário marcado para garantir a excelência que você merece.
                        </p>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="text-barber-primary">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="text-zinc-300">
                                    Av. Padre José Viégas, 328 - Recanto da Lagoa, Pará de Minas - MG, 35661-738
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-barber-primary">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-zinc-300">
                                    Seg a Sáb: 09:00h às 20:00h
                                </div>
                            </div>
                        </div>

                        <!-- Botão Gigante do WhatsApp -->
                        <div class="mt-12">
                            <a href="{{ route('scheduling') }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-3 px-8 py-5 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-full hover:bg-barber-accent transition-all shadow-[0_0_20px_rgba(212,175,55,0.3)] hover:shadow-[0_0_30px_rgba(212,175,55,0.5)] transform hover:-translate-y-1">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Agendar Horário
                            </a>
                        </div>
                    </div>
                    
                    <livewire:scheduling-wizard />
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#050505] py-12 border-t border-zinc-900">
        <div class="max-w-7xl mx-auto px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-6">
            <img class="h-10 w-auto opacity-50 hover:opacity-100 transition-opacity" src="{{ asset('images/logo.png') }}" alt="Logo">
            
            <p class="text-zinc-600 text-sm">
                &copy; {{ date('Y') }} Barbearia Barcelona. Creado para os exigentes.
            </p>
            
            <div class="flex gap-4">
                <a href="https://instagram.com/barbeariabarcelonaoficial" target="_blank" class="text-zinc-500 hover:text-barber-primary transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
                <a href="https://wa.me/5537999034539" target="_blank" class="text-zinc-500 hover:text-barber-primary transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/5537999034539" target="_blank" class="fixed bottom-6 right-6 md:bottom-10 md:right-10 z-[100] bg-[#25D366] text-white p-4 rounded-full shadow-[0_0_20px_rgba(37,211,102,0.4)] hover:bg-[#1ebd5a] hover:shadow-[0_0_30px_rgba(37,211,102,0.6)] transition-all duration-300 transform hover:-translate-y-2 hover:scale-110 flex items-center justify-center group" aria-label="Falar no WhatsApp">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        <span class="absolute right-full mr-4 bg-black/80 backdrop-blur-sm text-white text-xs font-bold py-2 px-3 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap shadow-lg">Falar no WhatsApp</span>
    </a>

    <!-- Scripts de Interação & Animação -->
    <script>
        // Menu Mobile Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function toggleMenu() {
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('flex');
            document.body.classList.toggle('overflow-hidden');
        }

        menuBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));

        // Animação no Scroll (Reveal Elements) & Header Background
        function onScroll() {
            // Header
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('bg-black/95', 'backdrop-blur-md', 'border-zinc-800');
                header.classList.remove('bg-transparent', 'border-transparent');
            } else {
                header.classList.remove('bg-black/95', 'backdrop-blur-md', 'border-zinc-800');
                header.classList.add('bg-transparent', 'border-transparent');
            }

            // Reveal
            var reveals = document.querySelectorAll(".reveal");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 100;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
        window.addEventListener("scroll", onScroll);
        // Chama uma vez para exibir elementos visíveis na carga
        onScroll();
    </script>
    
    @livewireScripts
</body>
</html>
