<div wire:poll.5s class="pb-24 md:pb-8">
    @section('header_title', 'Minha Agenda')

    <!-- Header Section -->
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl text-white font-serif mb-1 flex items-center gap-3">
                Minha Agenda
                <div class="inline-flex items-center gap-2 bg-[#111]/70 backdrop-blur-md border border-zinc-800 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-barber-primary animate-pulse"></span>
                    <span class="text-[9px] text-zinc-400 font-medium uppercase tracking-widest">Ao Vivo</span>
                </div>
            </h2>
            <p class="text-sm text-zinc-500">
                Seu dia, {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F') }}.
            </p>
        </div>
    </div>

    <!-- Indicadores Financeiros e Produção (Mobile Scrollable) -->
    <div class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar gap-3 md:grid md:grid-cols-4 md:gap-4 mb-8 -mx-4 px-4 md:mx-0 md:px-0">
        <div class="snap-center shrink-0 w-[140px] md:w-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-4 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/10 rounded-full blur-xl"></div>
            <p class="text-zinc-500 text-[9px] font-bold uppercase tracking-widest mb-1">Total na Agenda</p>
            <h3 class="text-2xl md:text-3xl text-white font-serif">{{ $stats['total_hoje'] }}</h3>
        </div>
        <div class="snap-center shrink-0 w-[140px] md:w-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-4 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-yellow-500/10 rounded-full blur-xl"></div>
            <p class="text-zinc-500 text-[9px] font-bold uppercase tracking-widest mb-1">Aguardando</p>
            <h3 class="text-2xl md:text-3xl text-white font-serif">{{ $stats['aguardando'] }}</h3>
        </div>
        <div class="snap-center shrink-0 w-[140px] md:w-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-4 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-500/10 rounded-full blur-xl"></div>
            <p class="text-zinc-500 text-[9px] font-bold uppercase tracking-widest mb-1">Concluídos</p>
            <h3 class="text-2xl md:text-3xl text-white font-serif">{{ $stats['concluidos'] }}</h3>
        </div>
        <div class="snap-center shrink-0 w-[150px] md:w-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-4 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-barber-primary/10 rounded-full blur-xl"></div>
            <p class="text-zinc-500 text-[9px] font-bold uppercase tracking-widest mb-1">Sua Comissão</p>
            <h3 class="text-2xl md:text-3xl text-white font-serif"><span class="text-barber-primary text-lg md:text-xl">R$</span> {{ number_format($stats['faturamento'], 2, ',', '.') }}</h3>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4 md:mb-6">
        <h3 class="text-lg md:text-xl text-white font-serif">Meus Clientes Hoje</h3>
        <span class="text-xs bg-zinc-800/50 text-zinc-400 px-2.5 py-1 rounded-full">{{ $appointments->count() }} agendamentos</span>
    </div>

    @if($appointments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            @foreach($appointments as $appt)
                <div class="bg-[#1c1c1c] border 
                    {{ $appt->status === 'in_progress' ? 'border-barber-primary shadow-[0_0_20px_rgba(237,179,41,0.3)] bg-[#2a2a2a]' : 'border-zinc-700' }} 
                    {{ $appt->status === 'rejected' || $appt->status === 'canceled' ? 'grayscale opacity-75 bg-[#151515]' : '' }}
                    {{ in_array($appt->status, ['awaiting_payment', 'completed']) ? 'border-green-500/50 bg-[#142217]' : '' }}
                    rounded-2xl p-4 md:p-5 flex flex-col transition-all duration-300 relative overflow-hidden">
                    
                    @if($appt->status === 'in_progress')
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-barber-primary to-transparent opacity-50"></div>
                    @endif

                    <!-- Header: Time & Status Badge -->
                    <div class="flex items-start justify-between border-b border-zinc-800/50 pb-3 md:pb-4 mb-3 md:mb-4">
                        <div>
                            <div class="text-2xl md:text-3xl text-white font-serif {{ in_array($appt->status, ['awaiting_payment', 'completed']) ? 'text-green-400' : '' }} leading-none mb-1">{{ substr($appt->start_time, 0, 5) }}</div>
                            <div class="text-[10px] text-zinc-500 uppercase tracking-widest">até {{ substr($appt->end_time, 0, 5) }}</div>
                        </div>
                        
                        <div class="text-right">
                            @if($appt->status === 'scheduled')
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest bg-zinc-800/50 text-zinc-400 border border-zinc-700">Aguardando</span>
                            @elseif($appt->status === 'in_progress')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest bg-barber-primary/20 text-barber-primary border border-barber-primary/30 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-barber-primary"></span>
                                    Em Cadeira
                                </span>
                            @elseif($appt->status === 'awaiting_payment')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest bg-green-500/10 text-green-400 border border-green-500/20">
                                    Fila Caixa
                                </span>
                            @elseif($appt->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Concluído
                                </span>
                            @elseif($appt->status === 'rejected' || $appt->status === 'canceled')
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[9px] font-bold uppercase tracking-widest bg-red-500/10 text-red-400 border border-red-500/20">
                                    Cancelado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="flex items-center gap-3 mb-3 md:mb-4">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-500 text-lg font-serif shrink-0">
                            {{ substr($appt->customer->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-base md:text-lg text-white font-medium mb-0.5 truncate">{{ $appt->customer->name }}</h4>
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $appt->customer->whatsapp) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-[#25D366] transition-colors">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', preg_replace('/[^0-9]/', '', $appt->customer->whatsapp)) }}
                            </a>
                        </div>
                    </div>

                    <!-- Service -->
                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800/50 rounded-xl p-3 flex items-center justify-between mb-4">
                        <div class="overflow-hidden pr-2">
                            <p class="text-[9px] text-zinc-500 uppercase tracking-widest mb-0.5">Serviço</p>
                            <p class="text-zinc-200 font-medium text-xs md:text-sm truncate">{{ $appt->service->name }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[9px] text-zinc-500 uppercase tracking-widest mb-0.5">Valor</p>
                            <p class="text-barber-primary font-bold text-xs md:text-sm">R$ {{ number_format($appt->service->price, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-auto flex flex-col gap-2">
                        @if($appt->status === 'scheduled')
                            <button wire:click="updateStatus({{ $appt->id }}, 'in_progress')" class="w-full bg-barber-primary hover:bg-barber-primary/90 text-[#111] font-bold py-3 md:py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(237,179,41,0.2)]">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Iniciar Atendimento
                            </button>
                            <button wire:click="updateStatus({{ $appt->id }}, 'rejected')" class="w-full bg-transparent border border-zinc-800/80 hover:border-red-500/50 text-zinc-400 hover:text-red-400 font-medium py-2 md:py-2.5 rounded-xl transition-colors text-xs md:text-sm">
                                Cliente não compareceu / Cancelar
                            </button>
                        @elseif($appt->status === 'in_progress')
                            <button wire:click="updateStatus({{ $appt->id }}, 'awaiting_payment')" class="w-full bg-green-500 hover:bg-green-400 text-[#111] font-bold py-3 md:py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2 shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Finalizar e Enviar Caixa
                            </button>
                        @elseif(in_array($appt->status, ['awaiting_payment', 'completed', 'rejected', 'canceled']))
                            <div class="w-full bg-[#0a0a0a]/50 border border-zinc-800/30 text-zinc-500 rounded-xl px-4 py-3 text-center text-xs md:text-sm font-medium">
                                @if($appt->status === 'awaiting_payment') 💳 Aguardando Pagamento
                                @elseif($appt->status === 'completed') ✅ Atendimento Concluído
                                @else ❌ Atendimento Cancelado @endif
                            </div>
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl md:rounded-3xl p-10 md:p-16 text-center max-w-2xl mx-auto mt-8 md:mt-12 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-b from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            <div class="w-16 h-16 md:w-24 md:h-24 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-full flex items-center justify-center mx-auto mb-6 md:mb-8 text-zinc-600 group-hover:text-barber-primary group-hover:scale-110 transition-all duration-500 shadow-[0_0_30px_rgba(0,0,0,0.5)]">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl md:text-3xl text-white font-serif mb-2 md:mb-4 relative z-10">Sua Agenda está Limpa</h3>
            <p class="text-zinc-500 text-sm md:text-lg relative z-10">Nenhum cliente agendado para você hoje até o momento.</p>
        </div>
    @endif
    
    <!-- CSS for hiding scrollbar in mobile horizontal scroll -->
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>
