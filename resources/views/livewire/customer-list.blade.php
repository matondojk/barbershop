<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <h2 class="text-2xl text-white font-serif">Gerenciar Clientes</h2>
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
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar cliente..." class="w-full pl-10 pr-4 py-2 bg-zinc-900/50 border border-zinc-700/50 rounded-lg text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm">
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">Nome</label>
                    <input wire:model.live.debounce.300ms="filter_name" type="text" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">CPF</label>
                    <input wire:model.live.debounce.300ms="filter_cpf" type="text" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-zinc-400 mb-1.5 uppercase tracking-wider">WhatsApp</label>
                    <input wire:model.live.debounce.300ms="filter_whatsapp" type="text" class="w-full bg-zinc-900/80 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-all text-xs">
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                        <th class="p-5 font-medium">Cliente</th>
                        <th class="p-5 font-medium">WhatsApp</th>
                        <th class="p-5 font-medium">CPF</th>
                        <th class="p-5 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-[#151515] transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-zinc-800 overflow-hidden flex-shrink-0 border border-zinc-700">
                                    <div class="w-full h-full flex items-center justify-center text-zinc-500 font-bold uppercase">{{ substr($customer->name, 0, 1) }}</div>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $customer->name }}</p>
                                    <p class="text-zinc-500 text-xs mt-0.5">{{ $customer->appointments_count }} agendamento(s)</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-zinc-400 text-sm">
                            @if($customer->whatsapp)
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $customer->whatsapp }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-5 text-zinc-400 text-sm">
                            {{ $customer->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $customer->cpf) : '-' }}
                        </td>
                        <td class="p-5 flex items-center justify-end gap-3 h-full">
                            <button wire:click="viewHistory({{ $customer->id }})" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-barber-primary hover:text-black bg-barber-primary/10 hover:bg-barber-primary border border-barber-primary/20 rounded-lg transition-colors" title="Ver Histórico">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Histórico
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center">
                            <div class="flex flex-col items-center justify-center text-zinc-500 gap-2">
                                <svg class="w-12 h-12 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <p>Nenhum cliente encontrado.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden flex flex-col divide-y divide-zinc-800/50">
            @forelse($customers as $customer)
            <div class="p-4 hover:bg-[#151515] transition-colors flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-zinc-800 overflow-hidden flex-shrink-0 border border-zinc-700">
                            <div class="w-full h-full flex items-center justify-center text-zinc-500 font-bold uppercase text-sm">{{ substr($customer->name, 0, 1) }}</div>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">{{ $customer->name }}</p>
                            <p class="text-zinc-500 text-[10px] mt-0.5">{{ $customer->appointments_count }} agendamento(s)</p>
                        </div>
                    </div>
                    
                    <div>
                        <button wire:click="viewHistory({{ $customer->id }})" class="p-2 text-barber-primary hover:text-black bg-barber-primary/10 hover:bg-barber-primary border border-barber-primary/20 rounded-lg transition-colors" title="Ver Histórico">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs mt-1">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-zinc-500 uppercase tracking-wider text-[9px] font-bold">CPF</span>
                        <span class="text-zinc-300 font-medium">{{ $customer->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $customer->cpf) : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col gap-0.5">
                        <span class="text-zinc-500 uppercase tracking-wider text-[9px] font-bold">WhatsApp</span>
                        @if($customer->whatsapp)
                            <span class="text-zinc-300 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $customer->whatsapp }}
                            </span>
                        @else
                            <span class="text-zinc-600 font-medium">-</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center flex flex-col items-center justify-center text-zinc-500 gap-2">
                <svg class="w-10 h-10 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <p class="text-sm">Nenhum cliente encontrado.</p>
            </div>
            @endforelse
        </div>
        
        <div class="p-4 border-t border-zinc-800">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- History Modal -->
    @if($showHistoryModal && $selectedCustomer)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-[#111] border border-zinc-800 rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-5 md:p-6 border-b border-zinc-800 flex items-center justify-between bg-[#1a1a1a]/40">
                <div class="flex flex-col gap-1">
                    <h3 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Histórico de {{ $selectedCustomer->name }}
                    </h3>
                    @if($selectedCustomer->whatsapp)
                        <span class="text-xs text-zinc-400 flex items-center gap-1">
                            <svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $selectedCustomer->whatsapp }}
                        </span>
                    @endif
                </div>
                <button wire:click="closeHistoryModal" class="p-2 text-zinc-400 hover:text-white bg-zinc-800/50 hover:bg-zinc-800 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 md:p-6 overflow-y-auto flex-1">
                @if(count($customerHistory) > 0)
                    <div class="space-y-4">
                        @foreach($customerHistory as $appointment)
                        <div class="p-4 border border-zinc-800/50 bg-[#151515] rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="text-white font-bold text-sm md:text-base">{{ $appointment->service->name ?? 'Serviço Removido' }}</h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-green-500/10 text-green-400 uppercase tracking-widest">
                                        Concluído
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-400 mt-2">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }} às {{ substr($appointment->start_time, 0, 5) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Barbeiro: {{ $appointment->user->name ?? 'Desconhecido' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex flex-row md:flex-col items-center md:items-end justify-between md:justify-center border-t md:border-t-0 md:border-l border-zinc-800/50 pt-3 md:pt-0 md:pl-4 mt-1 md:mt-0">
                                <span class="text-xs text-zinc-500 font-medium">Valor Pago</span>
                                <span class="text-barber-primary font-bold text-lg md:text-xl tracking-tight">R$ {{ number_format($appointment->amount_paid ?? ($appointment->service->price ?? 0), 2, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-zinc-500 py-10 gap-3">
                        <svg class="w-12 h-12 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Nenhuma marcação concluída encontrada para este cliente.</p>
                    </div>
                @endif
            </div>

            <div class="p-5 md:p-6 border-t border-zinc-800 flex justify-end bg-[#1a1a1a]/40">
                <button wire:click="closeHistoryModal" class="px-6 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-white font-medium rounded-xl transition-colors text-sm">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
