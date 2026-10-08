<div>
    @section('header_title', 'Gerenciar Cortes')

    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl text-white font-serif">Catálogo de Cortes</h2>
        <a href="{{ route('services.create') }}" class="px-6 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
            + Novo Corte
        </a>
    </div>

    <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                    <th class="p-5 font-medium">Corte / Procedimento</th>
                    <th class="p-5 font-medium">Preço</th>
                    <th class="p-5 font-medium">Duração</th>
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
                        <div class="flex flex-col gap-2 items-start">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-zinc-800 border border-zinc-700">
                                {{ $service->duration_minutes }} minutos
                            </span>
                            @if($service->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-green-500/10 text-green-400">ATIVO</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-red-500/10 text-red-400">INATIVO</span>
                            @endif
                        </div>
                    </td>
                    <td class="p-5 flex items-center justify-end gap-3 h-full pt-8">
                        <a wire:navigate href="{{ route('services.edit', $service->id) }}" class="p-2 text-zinc-400 hover:text-barber-primary bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Editar Corte">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                        <button wire:click="delete({{ $service->id }})" wire:confirm="Tem certeza que deseja remover este corte?" class="p-2 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Remover Corte">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-zinc-500">
                        Nenhum corte cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t border-zinc-800">
            {{ $services->links() }}
        </div>
    </div>
</div>
