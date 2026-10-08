<div>
    @section('header_title', 'Financeiro & Estatísticas')

    <div class="mb-8 md:mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-2xl md:text-3xl text-white font-serif mb-1 md:mb-2 flex items-center gap-3">
                Visão Financeira
            </h2>
            <p class="text-sm md:text-base text-zinc-500">
                Acompanhe o faturamento, comissões e estatísticas de vendas da barbearia.
            </p>
        </div>
        
        <!-- Period Selector -->
        <div class="flex items-center p-1 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-xl overflow-x-auto custom-scrollbar">
            <button wire:click="setPeriod('today')" class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $period === 'today' ? 'bg-barber-primary text-black shadow-md' : 'text-zinc-400 hover:text-white' }}">
                Hoje
            </button>
            <button wire:click="setPeriod('week')" class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $period === 'week' ? 'bg-barber-primary text-black shadow-md' : 'text-zinc-400 hover:text-white' }}">
                Esta Semana
            </button>
            <button wire:click="setPeriod('month')" class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $period === 'month' ? 'bg-barber-primary text-black shadow-md' : 'text-zinc-400 hover:text-white' }}">
                Este Mês
            </button>
            <button wire:click="setPeriod('year')" class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $period === 'year' ? 'bg-barber-primary text-black shadow-md' : 'text-zinc-400 hover:text-white' }}">
                Este Ano
            </button>
            <button wire:click="setPeriod('all')" class="px-4 py-2 text-sm font-medium rounded-lg transition-all whitespace-nowrap {{ $period === 'all' ? 'bg-barber-primary text-black shadow-md' : 'text-zinc-400 hover:text-white' }}">
                Tudo
            </button>
        </div>
    </div>

    <div wire:loading class="w-full text-center py-12">
        <svg class="animate-spin h-8 w-8 text-barber-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <p class="text-zinc-500 mt-4 font-medium">Calculando estatísticas...</p>
    </div>

    <div wire:loading.remove>
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8 md:mb-10">
            <!-- Faturamento Bruto -->
            <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-barber-primary/30 transition-all flex flex-col justify-between min-h-[140px]">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-barber-primary/5 rounded-full blur-xl group-hover:bg-barber-primary/10 transition-all"></div>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Receita Bruta
                </p>
                <div>
                    <h3 class="text-3xl lg:text-4xl text-white font-serif tracking-tight">R$ {{ number_format($stats['totalRevenue'], 2, ',', '.') }}</h3>
                    <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">Valor total pago pelos clientes</p>
                </div>
            </div>

            <!-- Lucro Líquido -->
            <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-green-500/30 transition-all flex flex-col justify-between min-h-[140px]">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/5 rounded-full blur-xl group-hover:bg-green-500/10 transition-all"></div>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Lucro da Barbearia
                </p>
                <div>
                    <h3 class="text-3xl lg:text-4xl text-white font-serif tracking-tight">R$ {{ number_format($stats['netProfit'], 2, ',', '.') }}</h3>
                    <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">Bruto abatido das comissões</p>
                </div>
            </div>

            <!-- Comissões -->
            <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-red-500/30 transition-all flex flex-col justify-between min-h-[140px]">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-xl group-hover:bg-red-500/10 transition-all"></div>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Comissões Pagas
                </p>
                <div>
                    <h3 class="text-3xl lg:text-4xl text-white font-serif tracking-tight">R$ {{ number_format($stats['totalCommission'], 2, ',', '.') }}</h3>
                    <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">Repasse total aos profissionais</p>
                </div>
            </div>

            <!-- Atendimentos -->
            <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group hover:border-zinc-500/30 transition-all flex flex-col justify-between min-h-[140px]">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-zinc-500/5 rounded-full blur-xl group-hover:bg-zinc-500/10 transition-all"></div>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    Atendimentos Concluídos
                </p>
                <div>
                    <h3 class="text-3xl lg:text-4xl text-white font-serif tracking-tight">{{ $stats['totalAppointments'] }}</h3>
                    <p class="text-[10px] text-zinc-600 mt-2 uppercase tracking-widest">No período selecionado</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Composição da Receita -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6">
                    <h3 class="text-white font-serif text-lg mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        Composição da Receita
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-zinc-400">Serviços (Cortes, etc)</span>
                                <span class="text-white font-bold">R$ {{ number_format($stats['servicesRevenue'], 2, ',', '.') }}</span>
                            </div>
                            @php
                                $totalComp = $stats['servicesRevenue'] + $stats['productsRevenue'];
                                $svcPct = $totalComp > 0 ? ($stats['servicesRevenue'] / $totalComp) * 100 : 0;
                                $prodPct = $totalComp > 0 ? ($stats['productsRevenue'] / $totalComp) * 100 : 0;
                            @endphp
                            <div class="w-full bg-zinc-900 rounded-full h-2">
                                <div class="bg-barber-primary h-2 rounded-full" style="width: {{ $svcPct }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-zinc-400">Produtos Vendidos</span>
                                <span class="text-white font-bold">R$ {{ number_format($stats['productsRevenue'], 2, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-zinc-900 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $prodPct }}%"></div>
                            </div>
                        </div>

                        @if($stats['totalDiscounts'] > 0)
                            <div class="pt-4 border-t border-zinc-800/50">
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-500">Descontos Concedidos</span>
                                    <span class="text-red-400 font-bold">- R$ {{ number_format($stats['totalDiscounts'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ranking -->
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top Serviços -->
                <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6">
                    <h3 class="text-white font-serif text-lg mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        Top Serviços Realizados
                    </h3>
                    
                    <div class="space-y-1">
                        @forelse($stats['topServices'] as $name => $count)
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-zinc-500 flex items-center justify-center font-bold text-xs border border-zinc-800">{{ $loop->iteration }}º</div>
                                    <span class="text-zinc-300 font-medium text-sm">{{ $name }}</span>
                                </div>
                                <span class="text-barber-primary font-bold text-sm">{{ $count }} vezes</span>
                            </div>
                        @empty
                            <div class="p-6 text-center text-zinc-500 text-sm">Nenhum serviço registrado neste período.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Produtos -->
                <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6">
                    <h3 class="text-white font-serif text-lg mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Top Produtos Vendidos
                    </h3>
                    
                    <div class="space-y-1">
                        @forelse($stats['topProducts'] as $name => $count)
                            <div class="flex items-center justify-between p-3 rounded-xl hover:bg-zinc-900/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-zinc-500 flex items-center justify-center font-bold text-xs border border-zinc-800">{{ $loop->iteration }}º</div>
                                    <span class="text-zinc-300 font-medium text-sm truncate max-w-[120px] md:max-w-[150px]">{{ $name }}</span>
                                </div>
                                <span class="text-green-500 font-bold text-sm">{{ $count }} unid.</span>
                            </div>
                        @empty
                            <div class="p-6 text-center text-zinc-500 text-sm">Nenhuma venda registrada neste período.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
