<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-2">
        <h2 class="text-2xl text-white font-serif">Conexões de WhatsApp</h2>
        <a wire:navigate href="{{ route('whatsapp.create') }}" class="px-6 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)] w-full md:w-auto text-center">
            + Nova Instância
        </a>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 font-medium">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($qrCode && $status === 'connecting')
        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden p-8 flex flex-col items-center justify-center relative" wire:poll.3s="checkStatus">
            <h3 class="text-2xl text-white font-serif mb-2">Escaneie o QR Code</h3>
            <p class="text-zinc-400 mb-6 font-medium tracking-wide">Conectando a instância: <span class="text-barber-primary font-bold">{{ $currentInstance }}</span></p>
            
            <div class="bg-white p-4 rounded-xl shadow-[0_0_30px_rgba(237,179,41,0.15)] relative overflow-hidden group">
                <img src="{{ str_starts_with($qrCode, 'data:image') ? $qrCode : 'data:image/png;base64,' . $qrCode }}" alt="QR Code" class="w-64 h-64">
                <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[2px]">
                    <button wire:click="connect('{{ $currentInstance }}')" class="text-barber-primary border border-barber-primary px-4 py-2 rounded font-bold uppercase tracking-widest text-xs hover:bg-barber-primary hover:text-black transition-colors">Atualizar QR</button>
                </div>
            </div>
            
            <button wire:click="connect('{{ $currentInstance }}')" class="mt-8 text-zinc-400 hover:text-barber-primary underline font-medium text-sm transition-colors">Gerar QR Code Novamente</button>
        </div>
    @elseif($status === 'connected')
        <div class="bg-[#111]/70 backdrop-blur-md border border-emerald-500/20 rounded-2xl overflow-hidden p-8 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl text-white font-serif mb-2">WhatsApp Conectado!</h3>
            <p class="text-zinc-400 font-medium tracking-wide">A instância <span class="text-emerald-400 font-bold">{{ $currentInstance }}</span> está autenticada e pronta para uso.</p>
        </div>
    @endif

    <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                        <th class="p-5 font-medium">Instância</th>
                        <th class="p-5 font-medium hidden md:table-cell">Descrição</th>
                        <th class="p-5 font-medium">Status / Função</th>
                        <th class="p-5 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="text-zinc-300">
                    @forelse($instances as $inst)
                        <tr class="border-b border-zinc-800 hover:bg-[#151515] transition-colors">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-barber-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </div>
                                    <span class="text-white font-medium">{{ $inst->name }}</span>
                                </div>
                            </td>
                            <td class="p-5 hidden md:table-cell">
                                <span class="text-zinc-400 text-sm">{{ $inst->description ?: '-' }}</span>
                            </td>
                            <td class="p-5">
                                @if($inst->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Ativa (Padrão)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-zinc-800 text-zinc-400 border border-zinc-700">
                                        Inativa
                                    </span>
                                @endif
                            </td>
                            <td class="p-5">
                                <div class="flex justify-end items-center gap-2">
                                    @if(!$inst->is_active)
                                        <button wire:click="activateInstance({{ $inst->id }})" class="px-3 py-1.5 text-zinc-400 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 border border-zinc-700/50 rounded transition-colors text-xs font-bold uppercase tracking-wider" title="Usar esta para envios">
                                            Tornar Principal
                                        </button>
                                    @endif
                                    
                                    <button wire:click="connect('{{ $inst->name }}')" class="p-2 text-zinc-400 hover:text-emerald-400 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Ver QR Code / Conectar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </button>
                                    
                                    <button wire:click="deleteInstance({{ $inst->id }})" wire:confirm="Tem certeza que deseja excluir esta instância do WhatsApp?" class="p-2 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Excluir">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center">
                                <div class="flex flex-col items-center justify-center text-zinc-500 gap-3">
                                    <svg class="w-12 h-12 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <p class="font-medium">Nenhuma instância cadastrada ainda.</p>
                                    <p class="text-sm">Crie uma nova instância acima para começar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-zinc-800">
            @forelse($instances as $inst)
                <div class="p-5 flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 shrink-0 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-barber-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-white font-medium break-all">{{ $inst->name }}</h3>
                                @if($inst->description)
                                    <p class="text-zinc-400 text-xs mt-0.5 line-clamp-2">{{ $inst->description }}</p>
                                @endif
                            </div>
                        </div>
                        @if($inst->is_active)
                            <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Ativa
                            </span>
                        @else
                            <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-800 text-zinc-400 border border-zinc-700">
                                Inativa
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex justify-between items-center pt-2">
                        @if(!$inst->is_active)
                            <button wire:click="activateInstance({{ $inst->id }})" class="px-3 py-1.5 text-zinc-400 hover:text-white bg-zinc-800/50 hover:bg-zinc-700 border border-zinc-700/50 rounded transition-colors text-[10px] font-bold uppercase tracking-wider" title="Usar esta para envios">
                                Tornar Principal
                            </button>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="flex gap-2">
                            <button wire:click="connect('{{ $inst->name }}')" class="p-2 text-zinc-400 hover:text-emerald-400 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Ver QR Code / Conectar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                            
                            <button wire:click="deleteInstance({{ $inst->id }})" wire:confirm="Tem certeza que deseja excluir esta instância do WhatsApp?" class="p-2 text-zinc-400 hover:text-red-500 bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors" title="Excluir">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 flex flex-col items-center justify-center text-zinc-500 gap-3">
                    <svg class="w-12 h-12 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p class="font-medium text-center">Nenhuma instância cadastrada ainda.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
