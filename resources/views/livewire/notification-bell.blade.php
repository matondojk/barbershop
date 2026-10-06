<div class="relative" wire:poll.10s>
    <button wire:click="toggle" class="relative p-2 text-zinc-400 hover:text-white transition-colors focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    @if($isOpen)
        <!-- Overlay -->
        <div class="fixed inset-0 z-40" wire:click="toggle"></div>
        
        <!-- Dropdown -->
        <div class="absolute right-0 mt-2 w-80 bg-[#111] border border-zinc-800 rounded-xl shadow-2xl z-50 overflow-hidden">
            <div class="p-4 border-b border-zinc-800 bg-[#0a0a0a]">
                <h3 class="text-white font-medium">Notificações</h3>
            </div>
            
            <div class="max-h-[350px] overflow-y-auto">
                @if($notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="p-4 border-b border-zinc-800/50 hover:bg-[#1a1a1a] transition-colors {{ is_null($notification->read_at) ? 'bg-[#151515]' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-barber-primary/20 text-barber-primary flex items-center justify-center shrink-0 mt-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-white leading-tight mb-1">
                                        Novo agendamento de <span class="font-bold text-barber-primary">{{ $notification->data['customer_name'] ?? 'Cliente' }}</span>
                                    </p>
                                    <p class="text-xs text-zinc-400">
                                        {{ \Carbon\Carbon::parse($notification->data['appointment_date'] ?? now())->format('d/m') }} às {{ substr($notification->data['start_time'] ?? '', 0, 5) }} - {{ $notification->data['service_name'] ?? 'Serviço' }}
                                    </p>
                                    <p class="text-[10px] text-zinc-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-zinc-500 text-sm">
                        Nenhuma notificação por enquanto.
                    </div>
                @endif
            </div>
            
            <div class="p-3 bg-[#0a0a0a] text-center border-t border-zinc-800">
                <a wire:navigate href="{{ route('dashboard') }}" class="text-xs text-barber-primary hover:text-white transition-colors">Ver Agenda Completa</a>
            </div>
        </div>
    @endif
</div>
