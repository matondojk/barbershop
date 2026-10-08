<div>
    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl text-white font-bold tracking-tight mb-1 md:mb-2">
                {{ $service_id ? 'Editar Serviço' : 'Novo Serviço' }}
            </h2>
            <p class="text-sm md:text-base text-zinc-400">Preencha os detalhes do serviço abaixo.</p>
        </div>
        <a wire:navigate href="{{ route('services.index') }}" class="w-full md:w-auto px-6 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 hover:bg-white/5 hover:text-white transition-all font-medium text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar
        </a>
    </div>

    <div class="w-full max-w-6xl">
        <div class="bg-[#0a0a0a]/70 backdrop-blur-xl border border-zinc-800/50 rounded-2xl md:rounded-3xl p-5 md:p-10 shadow-2xl">
            <form wire:submit="save" class="space-y-8 md:space-y-10">
                
                <div>
                    <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Detalhes Principais
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Nome do Serviço</label>
                            <input type="text" wire:model="name" required placeholder="Ex: Corte Degradê Clássico" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                            @error('name') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Preço (R$)</label>
                            <input type="text" wire:model="price" onkeyup="maskCurrency(this)" required placeholder="0,00" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                            @error('price') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Duração Média</label>
                            <select wire:model="duration_minutes" required class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium appearance-none">
                                <option value="">Selecione o tempo estimado...</option>
                                <option value="15">15 Minutos</option>
                                <option value="30">30 Minutos</option>
                                <option value="45">45 Minutos</option>
                                <option value="60">1 Hora (60 min)</option>
                                <option value="90">1 Hora e 30 Minutos (90 min)</option>
                                <option value="120">2 Horas (120 min)</option>
                            </select>
                            @error('duration_minutes') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Descrição (Opcional)</label>
                            <textarea wire:model="description" rows="3" placeholder="Detalhes sobre o que está incluso no serviço..." class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium"></textarea>
                            @error('description') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Status
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Visibilidade</label>
                            <select wire:model="is_active" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium appearance-none">
                                <option value="1">Ativo (Visível para agendamento)</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="pt-6 md:pt-8 border-t border-zinc-800/50 flex justify-end">
                    <button type="submit" class="w-full md:w-auto px-8 md:px-10 py-3.5 md:py-4 bg-barber-primary text-black font-extrabold uppercase tracking-widest text-sm rounded-xl hover:bg-barber-light transition-all shadow-[0_0_15px_rgba(237,179,41,0.15)] hover:shadow-[0_0_25px_rgba(237,179,41,0.3)] active:scale-95">
                        <span wire:loading.remove wire:target="save" class="hidden md:inline">{{ $service_id ? 'Salvar Alterações' : 'Cadastrar Serviço' }}</span>
                        <span wire:loading.remove wire:target="save" class="md:hidden">{{ $service_id ? 'Salvar' : 'Cadastrar' }}</span>
                        <span wire:loading wire:target="save" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Processando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function maskCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            input.value = value;
        }
    </script>
</div>
