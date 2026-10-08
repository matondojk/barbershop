<div class="max-w-4xl mx-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 md:p-10 my-10 relative z-20">
    <div class="mb-10 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 mx-auto mb-6 opacity-90">
        <h2 class="text-3xl text-white font-serif mb-2">Gerenciar Agendamento</h2>
        <p class="text-zinc-400">Aqui você pode alterar ou cancelar seu horário.</p>
    </div>

    @if($step === 'canceled')
        <div class="text-center py-10 max-w-lg mx-auto">
            <div class="w-24 h-24 bg-red-500/20 text-red-500 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4">Agendamento Cancelado</h3>
            <p class="text-zinc-400 mb-8">{{ $message ?: 'Este agendamento encontra-se cancelado.' }}</p>
            <a href="{{ route('scheduling') }}" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-xl hover:bg-barber-light transition-all">
                Fazer Novo Agendamento
            </a>
        </div>
    @elseif($step === 'past')
        <div class="text-center py-10 max-w-lg mx-auto">
            <div class="w-24 h-24 bg-zinc-800 text-zinc-400 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4">Agendamento Antigo</h3>
            <p class="text-zinc-400 mb-8">{{ $message }}</p>
            <a href="{{ route('scheduling') }}" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-xl hover:bg-barber-light transition-all">
                Fazer Novo Agendamento
            </a>
        </div>
    @elseif($step === 'success')
        <div class="text-center py-10 max-w-lg mx-auto">
            <div class="w-24 h-24 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 relative">
                <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
                <svg class="w-12 h-12 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4">Sucesso!</h3>
            <p class="text-zinc-400 mb-8 text-lg">{{ $message }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Detalhes Atuais -->
            <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 p-6 rounded-2xl h-fit">
                <h4 class="text-lg text-white font-serif mb-6 border-b border-zinc-800 pb-4">Dados Atuais</h4>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Cliente</span>
                        <span class="text-white font-medium">{{ $appointment->customer->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Data Original</span>
                        <span class="text-white font-medium">{{ $dateFormatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Barbeiro Atual</span>
                        <span class="text-white font-medium">{{ $appointment->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Serviço Atual</span>
                        <span class="text-white font-medium">{{ $appointment->service->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Hora Atual</span>
                        <span class="text-white font-medium">{{ substr($appointment->start_time, 0, 5) }}</span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-zinc-800">
                    <button wire:click="cancelAppointment" wire:confirm="Tem certeza que deseja cancelar definitivamente este agendamento?" class="w-full py-3 bg-red-500/10 text-red-500 font-bold uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all text-xs border border-red-500/30">
                        Cancelar Agendamento
                    </button>
                </div>
            </div>

            <!-- Formulário de Alteração -->
            <div>
                <h4 class="text-lg text-white font-serif mb-6">Fazer Alterações (Para o mesmo dia)</h4>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Profissional</label>
                        <select wire:model.live="barber_id" class="w-full bg-[#0a0a0a]/70 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:border-barber-primary focus:ring-1 focus:ring-barber-primary appearance-none">
                            <option value="">Selecione...</option>
                            @foreach($barbers as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Serviço</label>
                        <select wire:model.live="service_id" class="w-full bg-[#0a0a0a]/70 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:border-barber-primary focus:ring-1 focus:ring-barber-primary appearance-none">
                            <option value="">Selecione...</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->duration_minutes }} min) - R$ {{ number_format($s->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($barber_id && $service_id)
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Horários Disponíveis ({{ $dateFormatted }})</label>
                            
                            @if(count($available_slots) > 0)
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[200px] overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($available_slots as $time)
                                        <button wire:click="selectTime('{{ $time }}')" class="py-2 rounded-lg border text-center transition-all text-sm {{ $start_time === $time ? 'bg-barber-primary border-barber-primary text-black font-bold shadow-[0_0_10px_rgba(237,179,41,0.3)]' : 'bg-[#0a0a0a]/70 border-zinc-800 text-zinc-400 hover:border-barber-primary hover:text-white hover:bg-[#151515]' }}">
                                            {{ $time }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('start_time') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            @else
                                <div class="bg-zinc-900/50 border border-zinc-800 p-4 rounded-xl text-zinc-500 text-sm text-center">
                                    Nenhum horário disponível para este profissional neste dia.
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($start_time)
                        <button wire:click="updateAppointment" class="w-full py-4 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-xl hover:bg-barber-light transition-all flex justify-center items-center gap-2 shadow-[0_0_20px_rgba(237,179,41,0.3)] mt-6">
                            Salvar Alterações
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #0a0a0a; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
</style>
