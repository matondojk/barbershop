<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <h2 class="text-2xl text-white font-serif">Catálogo de Serviços</h2>
        <a href="{{ route('services.create') }}" class="px-6 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)] w-full md:w-auto text-center">
            <span class="md:hidden">+ Novo</span>
            <span class="hidden md:inline">+ Novo Serviço</span>
        </a>
    </div>

    <div x-data="{ showFilters: false }" class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden">
        
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-zinc-800 flex flex-col md:flex-row gap-4 items-center justify-between bg-[#1a1a1a]/40">
            <!-- Quick Search -->
            <div class="relative w-full md:max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar serviço..." class="w-full pl-10 pr-4 py-2 bg-zinc-900/50 border border-zinc-700/50 rounded-lg text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm">
            </div>

            <!-- Botão Filtros -->
            <button @click="showFilters = !showFilters" class="w-full md:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-zinc-900/50 border border-zinc-700/50 rounded-lg text-zinc-300 hover:text-white hover:border-zinc-600 transition-all font-medium text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filtros Avançados
            </button>
        </div>

        <!-- Painel de Filtros -->
        <div x-show="showFilters" x-transition style="display: none;" class="p-5 bg-[#151515] border-b border-zinc-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-white font-bold text-sm">Filtros Específicos</h3>
                <button wire:click="clearFilters" class="text-[10px] text-barber-primary hover:text-barber-light uppercase tracking-widest font-bold">Limpar Filtros</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Nome</label>
                    <input wire:model.live.debounce.300ms="filter_name" type="text" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Duração (minutos)</label>
                    <input wire:model.live.debounce.300ms="filter_duration" type="number" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Preço Mín.</label>
                    <input wire:model.live.debounce.300ms="filter_price_min" type="number" step="0.01" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Preço Máx.</label>
                    <input wire:model.live.debounce.300ms="filter_price_max" type="number" step="0.01" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                        <th class="p-5 font-medium">Serviço / Descrição</th>
                        <th class="p-5 font-medium">Preço</th>
                        <th class="p-5 font-medium">Duração</th>
                        <th class="p-5 font-medium">Status</th>
                        <th class="p-5 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse($services as $service)
                    <tr class="hover:bg-[#151515] transition-colors">
                        <td class="p-5">
                            <p class="text-white font-medium">{{ $service->name }}</p>
                            <p class="text-zinc-500 text-sm mt-1 max-w-sm truncate">{{ $service->description }}</p>
                        </td>
                        <td class="p-5 text-barber-primary font-medium">
                            R$ {{ number_format($service->price, 2, ',', '.') }}
                        </td>
                        <td class="p-5 text-zinc-400">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-zinc-800 border border-zinc-700">
                                {{ $service->duration_minutes }} minutos
                            </span>
                        </td>
                        <td class="p-5">
                            @if($service->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-green-500/10 text-green-400">ATIVO</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-red-500/10 text-red-400">INATIVO</span>
                            @endif
                        </td>
                        <td class="p-5 flex items-center justify-end gap-3 h-full">
                            <a wire:navigate href="{{ route('services.edit', $service->id) }}" class="p-2 text-zinc-400 hover:text-barber-primary bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Editar Serviço">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <button wire:click="delete({{ $service->id }})" wire:confirm="Tem certeza que deseja remover este serviço?" class="p-2 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Remover Serviço">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-500 gap-2">
                                <svg class="w-12 h-12 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                <p>Nenhum serviço encontrado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden flex flex-col divide-y divide-zinc-800/50">
            @forelse($services as $service)
            <div class="p-4 hover:bg-[#151515] transition-colors flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-white font-medium text-sm">{{ $service->name }}</p>
                        <p class="text-zinc-500 text-xs mt-0.5 line-clamp-2">{{ $service->description }}</p>
                        <div class="mt-2 flex gap-2">
                            @if($service->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-green-500/10 text-green-400">ATIVO</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-red-500/10 text-red-400">INATIVO</span>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-800 border border-zinc-700 text-zinc-300">
                                {{ $service->duration_minutes }} min
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end gap-2 shrink-0 ml-2">
                        <span class="text-barber-primary font-bold text-sm">
                            R$ {{ number_format($service->price, 2, ',', '.') }}
                        </span>
                        
                        <div class="flex items-center gap-1.5">
                            <a wire:navigate href="{{ route('services.edit', $service->id) }}" class="p-1.5 text-zinc-400 hover:text-barber-primary bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Editar Serviço">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <button wire:click="delete({{ $service->id }})" wire:confirm="Tem certeza que deseja remover este serviço?" class="p-1.5 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Remover Serviço">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center flex flex-col items-center justify-center text-zinc-500 gap-2">
                <svg class="w-10 h-10 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                <p class="text-sm">Nenhum serviço encontrado.</p>
            </div>
            @endforelse
        </div>
        
        <div class="p-4 border-t border-zinc-800">
            {{ $services->links() }}
        </div>
    </div>
</div>
