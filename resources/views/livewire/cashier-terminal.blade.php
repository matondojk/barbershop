<div wire:poll.5s>
    @section('header_title', 'Frente de Caixa (PDV)')

    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl text-white font-serif mb-2 flex items-center gap-3">
                Frente de Caixa
            </h2>
            <p class="text-zinc-500">
                Receba pagamentos e gerencie comissões dos atendimentos finalizados.
            </p>
        </div>
        
        <div class="w-full md:w-96 relative">
            <input type="text" wire:model.live.debounce.300ms="searchCpf" placeholder="Buscar por CPF do Cliente..." class="w-full bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-xl px-12 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
            <svg class="w-5 h-5 text-zinc-500 absolute left-4 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    @if($pendingPayments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($pendingPayments as $appt)
                <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-6 flex flex-col gap-6 relative overflow-hidden group hover:border-barber-primary/50 transition-colors">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-green-500/5 rounded-full blur-xl group-hover:bg-green-500/10 transition-all"></div>
                    
                    <div class="flex justify-between items-start">
                        <div>
                            @if($appt->status === 'awaiting_payment')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 mb-3">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Aguardando Pgto
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-barber-primary/20 text-barber-primary border border-barber-primary/30 mb-3 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-barber-primary"></span>
                                    Em Cadeira
                                </span>
                            @endif
                            <h4 class="text-xl text-white font-medium mb-1">{{ $appt->customer->name }}</h4>
                            <p class="text-sm text-zinc-500">
                                CPF: {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $appt->customer->cpf) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-1">Total</p>
                            <p class="text-2xl text-barber-primary font-serif font-bold">R$ {{ number_format($appt->service->price, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500">Serviço</span>
                            <span class="text-white font-medium">{{ $appt->service->name }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-zinc-500">Barbeiro</span>
                            <span class="text-white font-medium">{{ $appt->user->name }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm pt-3 border-t border-zinc-800/50">
                            <span class="text-zinc-500">Regra Comissão</span>
                            <span class="text-zinc-400">
                                {{ $appt->user->commission_type === 'percentage' ? $appt->user->commission_value . '%' : 'R$ ' . number_format($appt->user->commission_value, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <button wire:click="openCheckout({{ $appt->id }})" class="w-full py-3 bg-white text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-primary transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Receber Pagamento
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-3xl p-16 text-center max-w-2xl mx-auto mt-12">
            <div class="w-24 h-24 bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-full flex items-center justify-center mx-auto mb-8 text-zinc-600 shadow-[0_0_30px_rgba(0,0,0,0.5)]">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-3xl text-white font-serif mb-4">Caixa Livre</h3>
            <p class="text-zinc-500 text-lg">Nenhum cliente aguardando pagamento no momento.</p>
        </div>
    @endif

    <!-- Checkout Modal -->
    @if($checkoutAppointment)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl w-full max-w-2xl my-8 overflow-hidden shadow-2xl animate-fade-in-up">
                
                <div class="p-6 border-b border-zinc-800 flex justify-between items-center sticky top-0 bg-[#111]/90 backdrop-blur-md z-10">
                    <h3 class="text-xl text-white font-serif">Finalizar Pagamento</h3>
                    <button wire:click="closeCheckout" class="text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Detalhes do Serviço -->
                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md rounded-xl p-5 border border-zinc-800/50 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-zinc-500 mb-1">Serviço Principal</p>
                            <p class="text-white font-medium">{{ $checkoutAppointment->service->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl text-white font-bold">R$ {{ number_format($checkoutAppointment->service->price, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Produtos Consumidos -->
                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md rounded-xl p-5 border border-zinc-800/50 space-y-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                            <div class="w-full">
                                <label class="block text-sm font-medium text-zinc-400 mb-2">Adicionar Produto</label>
                                <div class="flex gap-2">
                                    <select wire:model="selectedProduct" class="w-full bg-zinc-900 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-colors text-sm">
                                        <option value="">Selecione um produto...</option>
                                        @foreach($availableProducts as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} - R$ {{ number_format($product->price, 2, ',', '.') }} (Estoque: {{ $product->stock_quantity }})</option>
                                        @endforeach
                                    </select>
                                    <button wire:click="addProduct" class="px-4 py-2 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors">
                                        Adicionar
                                    </button>
                                </div>
                                @error('selectedProduct') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Lista de Produtos Selecionados -->
                        @if(count($selectedProductsList) > 0)
                        <div class="space-y-2 mt-4">
                            @foreach($selectedProductsList as $index => $item)
                                <div class="flex items-center justify-between bg-zinc-900/50 rounded-lg p-3 border border-zinc-800/50">
                                    <div class="flex-1">
                                        <p class="text-white text-sm font-medium">{{ $item['name'] }}</p>
                                        <p class="text-zinc-500 text-xs">R$ {{ number_format($item['price'], 2, ',', '.') }} un.</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center bg-zinc-800 rounded-lg overflow-hidden">
                                            <button wire:click="decreaseQuantity({{ $index }})" class="px-2 py-1 text-zinc-400 hover:text-white hover:bg-zinc-700">-</button>
                                            <span class="px-3 text-sm text-white font-medium">{{ $item['quantity'] }}</span>
                                            <button wire:click="increaseQuantity({{ $index }})" class="px-2 py-1 text-zinc-400 hover:text-white hover:bg-zinc-700">+</button>
                                        </div>
                                        <p class="text-white font-bold w-20 text-right">R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</p>
                                        <button wire:click="removeProduct({{ $index }})" class="p-1 text-zinc-500 hover:text-red-500 transition-colors" title="Remover">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- Desconto e Total -->
                    <div class="bg-[#0a0a0a]/70 backdrop-blur-md rounded-xl p-5 border border-zinc-800/50 space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-zinc-800/50">
                            <div>
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Desconto Global (R$)</label>
                                <input type="text" wire:model.live.debounce.500ms="discount_amount" onkeyup="maskCurrency(this)" placeholder="0,00" class="w-32 bg-zinc-900 border border-zinc-700/50 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-barber-primary transition-colors text-sm">
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-zinc-500 mb-1">Total a Pagar</p>
                                <p class="text-3xl text-barber-primary font-bold">R$ {{ number_format($this->getExpectedTotal(), 2, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <!-- Forma de Pagamento -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-3">Forma de Pagamento</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="pix" class="peer sr-only">
                                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3 text-center hover:border-zinc-700 peer-checked:border-barber-primary peer-checked:text-barber-primary transition-all">
                                        <span class="font-medium text-sm">PIX</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="credit" class="peer sr-only">
                                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3 text-center hover:border-zinc-700 peer-checked:border-barber-primary peer-checked:text-barber-primary transition-all">
                                        <span class="font-medium text-sm">Crédito</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="debit" class="peer sr-only">
                                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3 text-center hover:border-zinc-700 peer-checked:border-barber-primary peer-checked:text-barber-primary transition-all">
                                        <span class="font-medium text-sm">Débito</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="payment_method" value="cash" class="peer sr-only">
                                    <div class="bg-zinc-900/50 border border-zinc-800 rounded-lg p-3 text-center hover:border-zinc-700 peer-checked:border-barber-primary peer-checked:text-barber-primary transition-all">
                                        <span class="font-medium text-sm">Dinheiro</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Valores -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-400 mb-2">Valor Recebido (R$)</label>
                                <input type="text" wire:model.live.debounce.500ms="amount_paid" onkeyup="maskCurrency(this)" class="w-full bg-zinc-900 border border-zinc-700/50 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary transition-colors text-lg">
                                @error('amount_paid') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-400 mb-2">Troco (R$)</label>
                                <div class="w-full bg-zinc-900/50 border border-zinc-800/50 rounded-lg px-4 py-3 text-emerald-400 font-bold text-lg cursor-not-allowed">
                                    {{ number_format($change_amount, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumo da Comissão -->
                        @php
                            $barber = $checkoutAppointment->user;
                            $servicePrice = $checkoutAppointment->service->price;
                            $commission = $barber->commission_type === 'percentage' 
                                ? $servicePrice * ($barber->commission_value / 100) 
                                : $barber->commission_value;
                        @endphp
                        <div class="bg-zinc-900/30 rounded-lg p-3 border border-zinc-800/50 flex justify-between items-center mt-2">
                            <span class="text-xs text-zinc-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Comissão p/ {{ $barber->name }} (Somente Serviço)
                            </span>
                            <span class="text-sm text-zinc-400 font-medium">R$ {{ number_format($commission, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-zinc-800 bg-[#0a0a0a]/70 backdrop-blur-md">
                    <button wire:click="confirmPayment" class="w-full py-4 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all shadow-[0_0_15px_rgba(237,179,41,0.2)]">
                        Confirmar Pagamento e Baixar
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <script>
        function maskCurrency(input) {
            let value = input.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            input.value = value;
            // Disparar evento para o Livewire atualizar
            input.dispatchEvent(new Event('input'));
        }
    </script>
</div>
