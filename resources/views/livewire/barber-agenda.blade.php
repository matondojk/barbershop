<div wire:poll.5s>
    @section('header_title', 'Painel do Barbeiro')

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl text-white font-serif mb-2 flex items-center gap-3">
                Resumo do Dia
            </h2>
            <p class="text-zinc-500">
                Acompanhe suas métricas e próximos atendimentos de hoje, {{ \Carbon\Carbon::now()->translatedFormat('l') }}.
            </p>
        </div>
        
        <div class="flex items-center gap-2 bg-[#111]/70 backdrop-blur-md border border-zinc-800 px-4 py-2 rounded-full">
            <span class="w-2 h-2 rounded-full bg-barber-primary animate-pulse"></span>
            <span class="text-xs text-zinc-400 font-medium uppercase tracking-widest">Painel Conectado</span>
        </div>
    </div>

    <!-- Indicadores Financeiros e Produção -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-all"></div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">Total na Agenda</p>
            <h3 class="text-3xl text-white font-serif">{{ $stats['total_hoje'] }}</h3>
        </div>
        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-yellow-500/10 rounded-full blur-xl group-hover:bg-yellow-500/20 transition-all"></div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">Aguardando</p>
            <h3 class="text-3xl text-white font-serif">{{ $stats['aguardando'] }}</h3>
        </div>
        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-500/10 rounded-full blur-xl group-hover:bg-green-500/20 transition-all"></div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">Serviços Concluídos</p>
            <h3 class="text-3xl text-white font-serif">{{ $stats['concluidos'] }}</h3>
        </div>
        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-barber-primary/10 rounded-full blur-xl group-hover:bg-barber-primary/20 transition-all"></div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-1">Sua Comissão</p>
            <h3 class="text-3xl text-white font-serif"><span class="text-barber-primary text-xl">R$</span> {{ number_format($stats['faturamento'], 2, ',', '.') }}</h3>
        </div>
    </div>

    <h3 class="text-xl text-white font-serif mb-6 border-b border-zinc-800 pb-4">Seus Clientes Hoje</h3>

    @if($appointments->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($appointments as $appt)
                <div class="bg-[#111]/70 backdrop-blur-md border 
                    {{ $appt->status === 'in_progress' ? 'border-barber-primary shadow-[0_0_15px_rgba(237,179,41,0.1)]' : 'border-zinc-800' }} 
                    {{ $appt->status === 'rejected' || $appt->status === 'canceled' ? 'opacity-50 grayscale' : '' }}
                    {{ in_array($appt->status, ['awaiting_payment', 'completed']) ? 'border-green-500/30 bg-green-500/5' : '' }}
                    rounded-2xl p-6 flex flex-col gap-6 transition-all duration-300">
                    
                    <!-- Header: Time & Status Badge -->
                    <div class="flex items-center justify-between border-b border-zinc-800/50 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl text-white font-serif {{ in_array($appt->status, ['awaiting_payment', 'completed']) ? 'text-green-400' : '' }}">{{ substr($appt->start_time, 0, 5) }}</span>
                            <span class="text-sm text-zinc-500">até {{ substr($appt->end_time, 0, 5) }}</span>
                        </div>
                        
                        <div>
                            @if($appt->status === 'scheduled')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-800 text-zinc-400 border border-zinc-700">Aguardando</span>
                            @elseif($appt->status === 'in_progress')
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-barber-primary/20 text-barber-primary border border-barber-primary/30 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-barber-primary"></span>
                                    Em Cadeira
                                </span>
                            @elseif($appt->status === 'awaiting_payment')
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-500/10 text-green-400 border border-green-500/20">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Fila do Caixa
                                </span>
                            @elseif($appt->status === 'completed')
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Pago
                                </span>
                            @elseif($appt->status === 'rejected' || $appt->status === 'canceled')
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancelado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Client & Service -->
                    <div class="flex-1">
                        <h4 class="text-xl text-white font-medium mb-1">{{ $appt->customer->name }}</h4>
                        <div class="flex items-center gap-2 text-sm text-zinc-400 mb-4">
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $appt->customer->whatsapp) }}" target="_blank" class="flex items-center gap-1.5 hover:text-[#25D366] transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', preg_replace('/[^0-9]/', '', $appt->customer->whatsapp)) }}
                            </a>
                        </div>
                        
                        <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Serviço</p>
                                <p class="text-white font-medium text-sm">{{ $appt->service->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Valor</p>
                                <p class="text-barber-primary font-bold">R$ {{ number_format($appt->service->price, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-zinc-800/50 flex flex-wrap gap-2">
                        @if($appt->status === 'awaiting_payment' || $appt->status === 'completed' || $appt->status === 'rejected' || $appt->status === 'canceled')
                            <div class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800/50 text-zinc-500 rounded-lg px-4 py-3 text-center text-sm font-medium opacity-70 cursor-not-allowed">
                                @if($appt->status === 'awaiting_payment') Aguardando Pagamento no Caixa 
                                @elseif($appt->status === 'completed') Pago / Finalizado
                                @else Atendimento Cancelado @endif
                            </div>
                        @else
                            <select wire:change="updateStatus({{ $appt->id }}, $event.target.value)" 
                                    class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border {{ $appt->status === 'in_progress' ? 'border-barber-primary/50 text-barber-primary' : 'border-zinc-700 text-white hover:border-zinc-500' }} rounded-lg px-4 py-3 focus:outline-none focus:border-barber-primary transition-colors appearance-none font-medium text-sm cursor-pointer shadow-sm">
                                <option value="scheduled" {{ $appt->status === 'scheduled' ? 'selected' : '' }}>⏳ Aguardando Cliente</option>
                                <option value="in_progress" {{ $appt->status === 'in_progress' ? 'selected' : '' }}>✂️ Iniciar Atendimento (Em Cadeira)</option>
                                <option value="awaiting_payment" {{ $appt->status === 'awaiting_payment' ? 'selected' : '' }}>💳 Enviar para o Caixa</option>
                                <option value="rejected" {{ $appt->status === 'rejected' ? 'selected' : '' }}>❌ Rejeitar / Cliente Faltou</option>
                            </select>
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-3xl p-16 text-center max-w-2xl mx-auto mt-12 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-b from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            <div class="w-24 h-24 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-full flex items-center justify-center mx-auto mb-8 text-zinc-600 group-hover:text-barber-primary group-hover:scale-110 transition-all duration-500 shadow-[0_0_30px_rgba(0,0,0,0.5)]">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4 relative z-10">Sua Agenda está Limpa</h3>
            <p class="text-zinc-500 text-lg relative z-10">Nenhum cliente agendado para você hoje até o momento.</p>
        </div>
    @endif
</div>
