<div>
    @section('header_title', 'Equipe & Colaboradores')

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <h2 class="text-2xl text-white font-serif">Membros da Equipe</h2>
        <a href="{{ route('collaborators.create') }}" class="px-6 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)] w-full md:w-auto text-center">
            + Novo Colaborador
        </a>
    </div>

    <!-- Filtros e Busca -->
    <div x-data="{ showFilters: false }" class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Quick Search -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nome, CPF ou WhatsApp..." class="w-full pl-11 pr-4 py-3 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-xl text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm">
            </div>

            <!-- Botão Filtros -->
            <button @click="showFilters = !showFilters" class="flex items-center justify-center gap-2 px-6 py-3 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-xl text-zinc-300 hover:text-white hover:border-zinc-700 transition-all font-medium text-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filtros Avançados
            </button>
        </div>

        <!-- Painel de Filtros -->
        <div x-show="showFilters" x-transition.opacity style="display: none;" class="mt-4 p-6 bg-[#111]/90 backdrop-blur-xl border border-zinc-800 rounded-2xl shadow-xl">
            <div class="flex justify-between items-center mb-6 border-b border-zinc-800/50 pb-4">
                <h3 class="text-white font-bold text-lg">Filtros Avançados</h3>
                <button wire:click="clearFilters" class="text-xs text-barber-primary hover:text-barber-light uppercase tracking-widest font-bold">Limpar Filtros</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Nome</label>
                    <input wire:model.live.debounce.300ms="filter_name" type="text" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-barber-primary transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">CPF</label>
                    <input wire:model.live.debounce.300ms="filter_cpf" type="text" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-barber-primary transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">WhatsApp</label>
                    <input wire:model.live.debounce.300ms="filter_whatsapp" type="text" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-barber-primary transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Início Expediente (a partir de)</label>
                    <input wire:model.live="filter_work_start_time" type="time" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-barber-primary transition-all text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-zinc-400 mb-2 uppercase tracking-wider">Fim Expediente (até)</label>
                    <input wire:model.live="filter_work_end_time" type="time" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-barber-primary transition-all text-sm">
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                    <th class="p-5 font-medium">Colaborador</th>
                    <th class="p-5 font-medium">Atribuições</th>
                    <th class="p-5 font-medium">Expediente</th>
                    <th class="p-5 font-medium">Status</th>
                    <th class="p-5 font-medium">Contato / Documento</th>
                    <th class="p-5 font-medium text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/50">
                @forelse($collaborators as $user)
                <tr class="hover:bg-[#151515] transition-colors">
                    <td class="p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-zinc-800 overflow-hidden flex-shrink-0 border border-zinc-700">
                                @if($user->photo_path)
                                    <img src="{{ Storage::url($user->photo_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-zinc-500 font-bold uppercase">{{ substr($user->name, 0, 1) }}</div>
                                @endif
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5 flex gap-2 flex-wrap">
                        @if(is_array($user->roles))
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    {{ $role === 'admin' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                                    {{ $role === 'barber' ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20' : '' }}
                                    {{ $role === 'financial' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                ">
                                    @if($role === 'admin') Administrador @endif
                                    @if($role === 'barber') Barbeiro @endif
                                    @if($role === 'financial') Financeiro @endif
                                </span>
                            @endforeach
                        @else
                            <span class="text-zinc-500 text-sm">Sem atribuições</span>
                        @endif
                    </td>
                    <td class="p-5 text-zinc-400 text-sm">
                        {{ substr($user->work_start_time, 0, 5) }} às {{ substr($user->work_end_time, 0, 5) }}
                    </td>
                    <td class="p-5">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-green-500/10 text-green-400">ATIVO</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-red-500/10 text-red-400">INATIVO</span>
                        @endif
                    </td>
                    <td class="p-5 text-zinc-400 text-sm">
                        <div class="flex flex-col gap-1">
                            @if($user->whatsapp)
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $user->whatsapp }}
                                </span>
                            @endif
                            <span class="flex items-center gap-2 text-zinc-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $user->cpf) }}
                            </span>
                        </div>
                    </td>
                    <td class="p-5 flex items-center justify-end gap-3">
                        <a wire:navigate href="{{ route('collaborators.edit', $user->id) }}" class="p-2 text-zinc-400 hover:text-barber-primary bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Editar Colaborador">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                        <button wire:click="delete({{ $user->id }})" wire:confirm="Tem certeza que deseja excluir este colaborador? Isso removerá o acesso dele ao sistema." class="p-2 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Remover Colaborador">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center">
                        <div class="flex flex-col items-center justify-center text-zinc-500 gap-2">
                            <svg class="w-12 h-12 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                            <p>Nenhum colaborador encontrado.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-4 border-t border-zinc-800">
            {{ $collaborators->links() }}
        </div>
    </div>
</div>
