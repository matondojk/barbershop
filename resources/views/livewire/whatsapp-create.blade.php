<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('whatsapp.index') }}" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl text-white font-serif">Nova Instância de WhatsApp</h2>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 font-medium flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulário -->
    <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden p-6 md:p-8 max-w-3xl">
        <form wire:submit="createInstance" class="space-y-6">
            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Nome da Instância</label>
                <input wire:model="name" type="text" placeholder="Ex: barbearia_matriz" class="w-full bg-zinc-900/50 border border-zinc-700/50 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all">
                @error('name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                <p class="text-zinc-500 text-xs mt-2">Nome único, sem espaços ou caracteres especiais (use underlines ou hifens).</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Descrição / Observação</label>
                <input wire:model="description" type="text" placeholder="Ex: Número usado para atendimento principal" class="w-full bg-zinc-900/50 border border-zinc-700/50 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all">
                @error('description') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex items-center gap-4">
                <button type="submit" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
                    Salvar Instância
                </button>
            </div>
        </form>
    </div>
</div>
