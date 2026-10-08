<div class="max-w-4xl mx-auto bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 md:p-10">
    
    <!-- Steps Indicator -->
    @if($step < 6)
    <div class="flex items-center justify-between mb-10 relative px-4">
        <div class="absolute left-8 right-8 top-1/2 -translate-y-1/2 h-0.5 bg-zinc-800 -z-10"></div>
        
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step >= 1 ? 'bg-barber-primary text-black ring-4 ring-barber-primary/20' : 'bg-zinc-800 text-zinc-500' }}">1</div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step >= 2 ? 'bg-barber-primary text-black ring-4 ring-barber-primary/20' : 'bg-zinc-800 text-zinc-500' }}">2</div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step >= 3 ? 'bg-barber-primary text-black ring-4 ring-barber-primary/20' : 'bg-zinc-800 text-zinc-500' }}">3</div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step >= 4 ? 'bg-barber-primary text-black ring-4 ring-barber-primary/20' : 'bg-zinc-800 text-zinc-500' }}">4</div>
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step >= 5 ? 'bg-barber-primary text-black ring-4 ring-barber-primary/20' : 'bg-zinc-800 text-zinc-500' }}">5</div>
    </div>
    @endif

    <!-- STEP 1: Seus Dados -->
    @if($step == 1)
        <div class="max-w-lg mx-auto">
            <h3 class="text-3xl text-white font-serif mb-6 text-center">Vamos começar?</h3>
            <p class="text-zinc-400 text-center mb-8">Informe seus dados para agilizar o agendamento.</p>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Qual seu nome?</label>
                    <input type="text" wire:model="customer_name" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-barber-primary" placeholder="Digite seu nome completo">
                    @error('customer_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Seu CPF</label>
                    <input type="text" wire:model="customer_cpf" oninput="maskCpf(this)" maxlength="14" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-barber-primary" placeholder="000.000.000-00">
                    @error('customer_cpf') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Seu WhatsApp</label>
                    <input type="text" wire:model="customer_whatsapp" oninput="maskWhatsApp(this)" maxlength="15" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-barber-primary" placeholder="(11) 99999-9999">
                    @error('customer_whatsapp') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <button wire:click="nextStep1" class="w-full mt-4 py-4 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-xl hover:bg-barber-light transition-all text-lg shadow-[0_0_20px_rgba(237,179,41,0.2)]">
                    Seguinte
                </button>
            </div>
        </div>
    @endif

    <!-- STEP 2: Escolher Barbeiro -->
    @if($step == 2)
        <div class="flex items-center gap-4 mb-8">
            <button wire:click="$set('step', 1)" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <div>
                <h3 class="text-2xl text-white font-serif">Escolha o Profissional</h3>
                <p class="text-zinc-500 text-sm">Com quem você deseja cortar?</p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($barbers as $barber)
            <div wire:click="selectBarber({{ $barber->id }})" class="cursor-pointer group bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 hover:border-barber-primary hover:bg-[#151515] rounded-2xl p-6 flex flex-col items-center text-center transition-all">
                <div class="w-24 h-24 rounded-full bg-zinc-800 overflow-hidden mb-4 border-2 border-transparent group-hover:border-barber-primary transition-all">
                    @if($barber->photo_path)
                        <img src="{{ Storage::url($barber->photo_path) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center font-bold text-2xl text-zinc-500">{{ substr($barber->name, 0, 1) }}</div>
                    @endif
                </div>
                <h4 class="text-white font-medium group-hover:text-barber-primary transition-colors text-lg">{{ $barber->name }}</h4>
            </div>
            @endforeach
        </div>
    @endif

    <!-- STEP 3: Escolher Serviço -->
    @if($step == 3)
        <div class="flex items-center gap-4 mb-8">
            <button wire:click="$set('step', 2)" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <div>
                <h3 class="text-2xl text-white font-serif">O que vamos fazer?</h3>
                <p class="text-zinc-500 text-sm">Selecione o corte ou procedimento desejado.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
            @foreach($services as $service)
            <div wire:click="selectService({{ $service->id }})" class="cursor-pointer group bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 hover:border-barber-primary hover:bg-[#151515] rounded-2xl p-5 flex flex-col justify-between transition-all h-full min-h-[140px]">
                <div>
                    <h4 class="text-white font-medium group-hover:text-barber-primary transition-colors leading-tight mb-2">{{ $service->name }}</h4>
                    <p class="text-xs text-zinc-500 bg-zinc-800 inline-block px-2 py-1 rounded-md">{{ $service->duration_minutes }} min</p>
                </div>
                <div class="text-barber-primary font-bold text-xl mt-4">
                    R$ {{ number_format($service->price, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- STEP 4: Escolher Data -->
    @if($step == 4)
        <div class="flex items-center gap-4 mb-8">
            <button wire:click="$set('step', 3)" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <div>
                <h3 class="text-2xl text-white font-serif">Escolha o Dia</h3>
                <p class="text-zinc-500 text-sm">Selecione uma data disponível.</p>
            </div>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-7 gap-3">
            @foreach($available_dates as $d)
                <button wire:click="selectDate('{{ $d['date'] }}')" class="py-4 px-2 rounded-xl border text-center transition-all flex flex-col items-center justify-center gap-1 hover:border-barber-primary hover:bg-[#151515] bg-[#0a0a0a]/70 backdrop-blur-md border-zinc-800">
                    <span class="text-xs text-zinc-500 uppercase tracking-widest">{{ $d['month_short'] }}</span>
                    <span class="text-2xl text-white font-serif">{{ $d['day_number'] }}</span>
                    <span class="text-xs text-barber-primary font-bold uppercase">{{ substr($d['day_name'], 0, 3) }}</span>
                </button>
            @endforeach
        </div>
    @endif

    <!-- STEP 5: Escolher Hora -->
    @if($step == 5)
        <div class="flex items-center gap-4 mb-8">
            <button wire:click="$set('step', 4)" class="w-10 h-10 rounded-full bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <div>
                <h3 class="text-2xl text-white font-serif">Qual o melhor horário?</h3>
                <p class="text-zinc-500 text-sm">Dia {{ \Carbon\Carbon::parse($appointment_date)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="space-y-6">
            @if($existing_appointment)
                <div class="bg-red-500/10 border border-red-500/30 p-6 rounded-2xl text-center max-w-lg mx-auto">
                    <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h4 class="text-xl text-white font-serif mb-2">Atenção!</h4>
                    <p class="text-zinc-400 text-sm mb-6">Você já possui um horário marcado para o dia {{ \Carbon\Carbon::parse($appointment_date)->format('d/m/Y') }}. Não permitimos mais de um agendamento no mesmo dia para evitar ausências.</p>
                    
                    <button wire:click="rescheduleExisting" class="w-full py-4 bg-red-500 text-white font-bold uppercase tracking-widest rounded-xl hover:bg-red-600 transition-all">
                        Desejo Remarcar (Cancelar o anterior)
                    </button>
                </div>
            @else
                @if(count($available_slots) > 0)
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($available_slots as $time)
                            <button wire:click="selectTime('{{ $time }}')" class="py-3 rounded-xl border text-center transition-all text-lg {{ $start_time === $time ? 'bg-barber-primary border-barber-primary text-black font-bold shadow-[0_0_15px_rgba(237,179,41,0.3)]' : 'bg-[#0a0a0a]/70 backdrop-blur-md border-zinc-800 text-zinc-400 hover:border-barber-primary hover:text-white hover:bg-[#151515]' }}">
                                {{ $time }}
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-8 text-center">
                        <svg class="w-12 h-12 text-zinc-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h4 class="text-white font-medium mb-2">Agenda Lotada</h4>
                        <p class="text-zinc-500">Infelizmente não há horários disponíveis para este dia. Por favor, volte e escolha outra data.</p>
                    </div>
                @endif
                @error('start_time') <span class="text-red-500 text-sm mt-1 block text-center">{{ $message }}</span> @enderror
            @endif

            @if($start_time && !$existing_appointment)
                <div class="pt-8 flex justify-center">
                    <button wire:click="confirm" wire:loading.attr="disabled" class="w-full max-w-md py-4 bg-barber-primary text-black font-bold uppercase tracking-widest rounded-xl hover:bg-barber-light transition-all flex justify-center items-center gap-2 shadow-[0_0_20px_rgba(237,179,41,0.3)] text-lg">
                        <span wire:loading.remove wire:target="confirm">Confirmar Agendamento</span>
                        <span wire:loading wire:target="confirm">Processando...</span>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- STEP 6: Sucesso -->
    @if($step == 6)
        <div class="text-center py-10 max-w-lg mx-auto">
            <div class="w-24 h-24 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 relative">
                <div class="absolute inset-0 bg-green-500/20 rounded-full animate-ping"></div>
                <svg class="w-12 h-12 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="text-4xl text-white font-serif mb-4">Agendamento Confirmado!</h3>
            <p class="text-zinc-400 mb-10 text-lg">Te esperamos na barbearia no dia <strong class="text-white">{{ \Carbon\Carbon::parse($appointment_date)->format('d/m/Y') }}</strong> às <strong class="text-white">{{ $start_time }}</strong>.</p>
            
            <button wire:click="$set('step', 1)" class="px-10 py-4 border-2 border-zinc-700 text-white rounded-xl hover:bg-zinc-800 hover:border-zinc-600 transition-all uppercase tracking-widest text-sm font-bold">
                Fazer Novo Agendamento
            </button>
        </div>
    @endif

</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #0a0a0a; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
    .custom-calendar-icon::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
</style>

<script>
    function maskCpf(input) {
        let v = input.value.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        input.value = v;
    }

    function maskWhatsApp(input) {
        let v = input.value.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 2) {
            v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
        }
        if (v.length > 9) {
            v = v.replace(/(\d{5})(\d)/, "$1-$2");
        } else if (v.length > 8) {
            v = v.replace(/(\d{4})(\d)/, "$1-$2");
        }
        input.value = v;
    }
</script>
