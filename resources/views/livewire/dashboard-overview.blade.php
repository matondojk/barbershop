<div wire:poll.2s>
    @section('header_title', 'Agenda de Hoje - ' . date('d/m/Y'))

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl text-white font-serif mb-2 flex items-center gap-3">
                @if(auth()->user()->isAdmin())
                    Visão Geral
                    <span class="text-sm font-sans bg-barber-primary/10 text-barber-primary px-3 py-1 rounded-full border border-barber-primary/20">Todos os Barbeiros</span>
                @else
                    Sua Agenda do Dia
                @endif
            </h2>
            <p class="text-zinc-500">
                Acompanhe os cortes agendados para hoje, <span class="text-zinc-300">{{ \Carbon\Carbon::now()->translatedFormat('l') }}</span>.
            </p>
        </div>
        
        <div class="flex items-center gap-2 bg-[#111] border border-zinc-800 px-4 py-2 rounded-full">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-xs text-zinc-400 font-medium uppercase tracking-widest">Atualização em tempo real</span>
        </div>
    </div>

    @if($appointments->count() > 0)
        <div class="space-y-5">
            @foreach($appointments as $appt)
                <div class="group relative bg-[#0a0a0a] border {{ $appt->status == 'scheduled' ? 'border-zinc-800 hover:border-barber-primary/50' : 'border-zinc-800 opacity-50' }} rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 transition-all duration-300 overflow-hidden">
                    
                    <!-- Glow Effect on Hover -->
                    <div class="absolute -inset-px bg-gradient-to-r from-barber-primary/0 via-barber-primary/10 to-barber-primary/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                    <!-- Time & Status -->
                    <div class="flex items-center gap-6 min-w-[220px] relative z-10">
                        <div class="text-center bg-[#111] rounded-xl px-5 py-3 border border-zinc-800/50 group-hover:border-barber-primary/30 transition-colors">
                            <h3 class="text-3xl text-white font-serif tracking-wide">{{ substr($appt->start_time, 0, 5) }}</h3>
                            <p class="text-xs text-zinc-500 mt-1 uppercase tracking-widest">Até {{ substr($appt->end_time, 0, 5) }}</p>
                        </div>
                        
                        <div>
                            @if($appt->status === 'scheduled')
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-zinc-800 text-zinc-400 border border-zinc-700">
                                        Aguardando
                                    </span>
                                </div>
                            @elseif($appt->status === 'in_progress')
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-barber-primary/20 text-barber-primary border border-barber-primary/30 animate-pulse">
                                        Em Atendimento
                                    </span>
                                </div>
                            @elseif($appt->status === 'completed')
                                <div class="flex flex-col gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-green-500/10 text-green-400 border border-green-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        Finalizado
                                    </span>
                                </div>
                            @elseif($appt->status === 'rejected' || $appt->status === 'canceled')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-red-500/10 text-red-400 border border-red-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Cancelado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="flex-1 relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-xl text-white font-medium">{{ $appt->customer->name }}</h4>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-400">
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $appt->customer->whatsapp) }}" target="_blank" class="flex items-center gap-1.5 hover:text-[#25D366] transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', preg_replace('/[^0-9]/', '', $appt->customer->whatsapp)) }}
                            </a>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $appt->customer->cpf) }}
                            </span>
                        </div>
                    </div>

                    <!-- Service & Barber Info -->
                    <div class="flex items-center gap-6 min-w-[300px] relative z-10">
                        <div class="flex-1 text-right">
                            <h4 class="text-white font-medium flex items-center justify-end gap-2">
                                {{ $appt->service->name }}
                                <svg class="w-4 h-4 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                            </h4>
                            <p class="text-sm text-barber-primary font-bold mt-1">R$ {{ number_format($appt->service->price, 2, ',', '.') }}</p>
                        </div>
                        
                        @if(auth()->user()->isAdmin())
                            <div class="h-12 w-px bg-zinc-800"></div>
                            <div class="flex items-center gap-3 bg-[#111] p-2 pr-4 rounded-full border border-zinc-800">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 overflow-hidden border border-zinc-700 shrink-0">
                                    @if($appt->user->photo_path)
                                        <img src="{{ Storage::url($appt->user->photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-bold text-zinc-500">{{ substr($appt->user->name, 0, 1) }}</div>
                                    @endif
                                </div>
                                <div class="text-left">
                                    <p class="text-[10px] text-zinc-500 uppercase tracking-widest leading-none mb-1">Profissional</p>
                                    <p class="text-sm text-white font-medium leading-none">{{ explode(' ', $appt->user->name)[0] }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#0a0a0a] border border-zinc-800 rounded-3xl p-16 text-center max-w-2xl mx-auto mt-12 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-b from-barber-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
            <div class="w-24 h-24 bg-[#111] border border-zinc-800 rounded-full flex items-center justify-center mx-auto mb-8 text-zinc-600 group-hover:text-barber-primary group-hover:scale-110 transition-all duration-500 shadow-[0_0_30px_rgba(0,0,0,0.5)]">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4 relative z-10">Nenhum agendamento para hoje</h3>
            <p class="text-zinc-500 text-lg relative z-10">Sua agenda está livre. Aproveite para organizar a bancada!</p>
        </div>
    @endif
</div>
