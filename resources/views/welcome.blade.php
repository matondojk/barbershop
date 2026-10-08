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
<body class="bg-[#0a0a0a]/70 backdrop-blur-md text-zinc-300 antialiased selection:bg-barber-primary selection:text-black overflow-x-hidden">

    <!-- Header / Navbar -->
    <header id="header" class="fixed w-full top-0 z-50 transition-all duration-500 py-4">
        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="flex justify-between items-center bg-[#111]/70 backdrop-blur-md/80 backdrop-blur-md rounded-full border border-zinc-800/50 px-6 py-3 shadow-2xl">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Barbearia Barcelona" class="h-10 w-auto object-contain transition-transform duration-500 group-hover:scale-110">
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#home" class="text-sm font-medium uppercase tracking-widest text-zinc-400 hover:text-barber-primary transition-colors">Início</a>
                    <a href="#services" class="text-sm font-medium uppercase tracking-widest text-zinc-400 hover:text-barber-primary transition-colors">Serviços</a>
                    <a href="#about" class="text-sm font-medium uppercase tracking-widest text-zinc-400 hover:text-barber-primary transition-colors">A Barbearia</a>
                    <a href="#gallery" class="text-sm font-medium uppercase tracking-widest text-zinc-400 hover:text-barber-primary transition-colors">Galeria</a>
                </nav>

                <!-- CTA Desktop -->
                <div class="hidden md:block">
                    <a href="{{ route('scheduling') }}" class="px-6 py-2.5 bg-barber-primary text-black text-sm font-bold uppercase tracking-wider rounded-full hover:bg-barber-light transition-all shadow-[0_0_15px_rgba(237,179,41,0.3)] hover:shadow-[0_0_25px_rgba(237,179,41,0.6)]">
                        Agendar
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden text-barber-primary focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Nav (Dropdown) -->
        <div id="mobile-menu" class="fixed inset-0 bg-black/95 z-40 hidden flex-col items-center justify-center space-y-8 backdrop-blur-lg">
            <button id="close-menu-btn" class="absolute top-10 right-10 text-zinc-400 hover:text-white">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <a href="#home" class="mobile-link text-2xl font-serif text-white hover:text-barber-primary transition-colors">Início</a>
            <a href="#services" class="mobile-link text-2xl font-serif text-white hover:text-barber-primary transition-colors">Serviços</a>
            <a href="#about" class="mobile-link text-2xl font-serif text-white hover:text-barber-primary transition-colors">A Barbearia</a>
            <a href="#gallery" class="mobile-link text-2xl font-serif text-white hover:text-barber-primary transition-colors">Galeria</a>
            <a href="{{ route('scheduling') }}" class="mobile-link mt-8 px-8 py-4 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-full">Agendar Agora</a>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background elements -->
        <div class="absolute inset-0 z-0">
            <!-- Imagem escura e profissional com alto contraste -->
            <img src="https://images.unsplash.com/photo-1593702295094-aea22597af65?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Barbearia Fundo" class="w-full h-full object-cover opacity-40">
            <!-- Gradiente para escurecer a base e dar foco no texto -->
            <div class="absolute inset-0 bg-gradient-to-b from-[#0a0a0a]/50 via-[#0a0a0a]/70 to-[#0a0a0a]"></div>
            <!-- Detalhe de luz dourada radial -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(237,179,41,0.08)_0,transparent_50%)]"></div>
        </div>

        <div class="relative z-10 text-center px-4 w-full max-w-5xl mx-auto mt-20 reveal">
            <span class="block text-barber-primary tracking-[0.3em] text-sm md:text-base font-semibold uppercase mb-4">A Experiência Definitiva</span>
            <h1 class="text-5xl md:text-8xl text-white font-serif mb-6 leading-tight drop-shadow-2xl">
                Cortes de <br>
                <span class="italic font-light text-transparent bg-clip-text bg-gradient-to-r from-barber-light via-barber-primary to-barber-dark">Alto Padrão</span>
            </h1>
            <p class="text-lg md:text-xl text-zinc-400 mb-12 max-w-2xl mx-auto font-light">
                Para o homem que não abre mão de um visual impecável. Muito mais que uma barbearia, um clube exclusivo de estilo e sofisticação.
            </p>
            
            <!-- Botão de Agendamento Principal - Pulsante -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('scheduling') }}" class="btn-glow px-10 py-5 bg-gradient-to-r from-barber-primary to-barber-dark text-black text-lg font-bold uppercase tracking-widest rounded-full transition-all">
                    Agendar Meu Horário
                </a>
            </div>
        </div>

        <!-- Scroll Down Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center animate-bounce z-10">
            <span class="text-xs text-zinc-500 uppercase tracking-widest mb-2">Deslize</span>
            <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-32 relative bg-[#0a0a0a]/70 backdrop-blur-md">
        <!-- Linha divisória de luz -->
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-barber-primary/30 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6 md:px-12">
            <div class="text-center mb-20 reveal">
                <span class="text-barber-primary tracking-[0.2em] text-sm font-semibold uppercase">Menu de</span>
                <h2 class="text-4xl md:text-6xl text-white font-serif mt-2">Serviços Premium</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="glass rounded-2xl p-10 group hover:-translate-y-2 transition-all duration-500 hover:border-barber-primary/50 relative overflow-hidden reveal">
                    <div class="absolute inset-0 bg-gradient-to-br from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 border border-barber-primary/30 rounded-full flex items-center justify-center mb-8 text-barber-primary group-hover:bg-barber-primary group-hover:text-black transition-colors duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                        </div>
                        <div class="flex justify-between items-baseline mb-4 border-b border-zinc-800 pb-4">
                            <h3 class="text-2xl text-white font-serif">Corte Executivo</h3>
                            <span class="text-xl font-medium text-barber-primary">R$ 60</span>
                        </div>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            Visagismo, lavagem relaxante, corte com técnicas avançadas (tesoura ou fade) e finalização com pomada importada.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="glass rounded-2xl p-10 group hover:-translate-y-2 transition-all duration-500 hover:border-barber-primary/50 relative overflow-hidden reveal" style="transition-delay: 100ms;">
                    <div class="absolute inset-0 bg-gradient-to-br from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 border border-barber-primary/30 rounded-full flex items-center justify-center mb-8 text-barber-primary group-hover:bg-barber-primary group-hover:text-black transition-colors duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 14.321V21h12v-6.679M9 10V3m6 7V3m-9 7h12a2 2 0 012 2v2H4v-2a2 2 0 012-2z"/></svg>
                        </div>
                        <div class="flex justify-between items-baseline mb-4 border-b border-zinc-800 pb-4">
                            <h3 class="text-2xl text-white font-serif">Barba Clássica</h3>
                            <span class="text-xl font-medium text-barber-primary">R$ 50</span>
                        </div>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            Ritual tradicional: toalha quente, espumas aromáticas, navalha e hidratação com óleos essenciais para uma pele perfeita.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="glass rounded-2xl p-10 group hover:-translate-y-2 transition-all duration-500 hover:border-barber-primary/50 relative overflow-hidden reveal" style="transition-delay: 200ms;">
                    <div class="absolute inset-0 bg-gradient-to-br from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative z-10">
                        <!-- Glow label -->
                        <div class="absolute top-0 right-0 bg-barber-primary text-black text-xs font-bold px-3 py-1 uppercase tracking-widest rounded-bl-lg">Mais Pedido</div>
                        
                        <div class="w-14 h-14 border border-barber-primary/30 rounded-full flex items-center justify-center mb-8 text-barber-primary group-hover:bg-barber-primary group-hover:text-black transition-colors duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <div class="flex justify-between items-baseline mb-4 border-b border-zinc-800 pb-4">
                            <h3 class="text-2xl text-white font-serif">O Combo</h3>
                            <span class="text-xl font-medium text-barber-primary">R$ 100</span>
                        </div>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            A transformação completa. Corte impecável alinhado com a barboterapia, garantindo o máximo de relaxamento e estilo.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-16 text-center reveal">
                <a href="{{ route('scheduling') }}" class="inline-flex items-center gap-3 text-barber-primary hover:text-white transition-colors font-medium tracking-widest uppercase text-sm border-b border-barber-primary hover:border-white pb-1">
                    Agende agora e garanta seu horário
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
                    <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Ambiente Barbearia" class="relative rounded-2xl shadow-2xl grayscale hover:grayscale-0 transition-all duration-700 w-full h-[600px] object-cover">
                    
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
                <div class="glass p-8 rounded-2xl border border-zinc-800 reveal transition-transform duration-300 hover:-translate-y-2">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 italic mb-6">"Uma experiência inexplicável, ótimo atendimento e excelentes profissionais!!"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-xl font-bold text-white">T</div>
                        <div>
                            <h4 class="text-white font-semibold">Thiago Contemplacar</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 2 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 100ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 italic mb-6">"Top demais! Parabéns para o Nagai e Rafael. Muito top!🤩🙌🏻❤🙏🏼"</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-xl font-bold text-white">E</div>
                        <div>
                            <h4 class="text-white font-semibold">Erikin Guedes</h4>
                        </div>
                    </div>
                </div>

                <!-- Avaliação 3 -->
                <div class="glass p-8 rounded-2xl border border-zinc-800 reveal transition-transform duration-300 hover:-translate-y-2" style="transition-delay: 200ms;">
                    <div class="flex items-center gap-2 mb-4 text-barber-primary">
                        @for($i=0; $i<5; $i++)
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-zinc-400 italic mb-6">"Melhor barbearia de Para de Minas."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-zinc-800 rounded-full flex items-center justify-center text-xl font-bold text-white">M</div>
                        <div>
                            <h4 class="text-white font-semibold">Marcus Vinícius</h4>
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
                <a href="#" class="text-zinc-500 hover:text-barber-primary transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                </a>
            </div>
        </div>
    </footer>

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

        // Animação no Scroll (Reveal Elements)
        function reveal() {
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
        window.addEventListener("scroll", reveal);
        // Chama uma vez para exibir elementos visíveis na carga
        reveal();
    </script>
    
    @livewireScripts
</body>
</html>
