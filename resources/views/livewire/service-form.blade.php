<div>
    @section('header_title', $service_id ? 'Editar Corte' : 'Cadastrar Corte')

    <div class="max-w-2xl">
        <div class="mb-8">
            <a wire:navigate href="{{ route('services.index') }}" class="text-zinc-500 hover:text-white transition-colors flex items-center gap-2 text-sm uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Voltar
            </a>
        </div>

        <div class="bg-[#111] border border-zinc-800 rounded-2xl p-8">
            <h3 class="text-xl text-white font-serif mb-6">{{ $service_id ? 'Editar Informações do Corte' : 'Detalhes do Novo Corte' }}</h3>
            
            <form wire:submit="save" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Nome do Corte</label>
                    <input type="text" wire:model="name" required placeholder="Ex: Corte Degadê Clássico" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                    @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Preço (R$)</label>
                    <input type="text" wire:model="price" onkeyup="maskCurrency(this)" required placeholder="0,00" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                    @error('price') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Duração Média (em minutos)</label>
                    <select wire:model="duration_minutes" required class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors appearance-none">
                        <option value="">Selecione o tempo estimado...</option>
                        <option value="15">15 Minutos</option>
                        <option value="30">30 Minutos</option>
                        <option value="45">45 Minutos</option>
                        <option value="60">1 Hora (60 min)</option>
                        <option value="90">1 Hora e 30 Minutos (90 min)</option>
                        <option value="120">2 Horas (120 min)</option>
                    </select>
                    @error('duration_minutes') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Descrição (Opcional)</label>
                    <textarea wire:model="description" rows="3" placeholder="Detalhes sobre o que está incluso no corte..." class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors"></textarea>
                    @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Status do Corte</label>
                    <select wire:model="is_active" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary transition-colors appearance-none">
                        <option value="1">Ativo (Visível para agendamento)</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>

                <div class="pt-6 border-t border-zinc-800 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
                        <span wire:loading.remove wire:target="save">{{ $service_id ? 'Salvar Alterações' : 'Salvar Corte' }}</span>
                        <span wire:loading wire:target="save">Salvando...</span>
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
