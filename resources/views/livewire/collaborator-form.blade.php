<div>
    @section('header_title', 'Equipe')

    <div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl text-white font-bold tracking-tight mb-1 md:mb-2">
                {{ $user_id ? 'Editar Colaborador' : 'Novo Colaborador' }}
            </h2>
            <p class="text-sm md:text-base text-zinc-400">Preencha os dados abaixo para gerenciar o acesso.</p>
        </div>
        <a wire:navigate href="{{ route('collaborators.index') }}" class="w-full md:w-auto px-6 py-2.5 rounded-xl border border-zinc-700 text-zinc-300 hover:bg-white/5 hover:text-white transition-all font-medium text-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar
        </a>
    </div>

    <div class="w-full max-w-6xl">
        <div class="bg-[#0a0a0a]/70 backdrop-blur-xl border border-zinc-800/50 rounded-2xl md:rounded-3xl p-5 md:p-10 shadow-2xl">
            <form wire:submit="save" class="space-y-8 md:space-y-10">
                
                <!-- Dados Básicos -->
                <div>
                    <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Dados Pessoais
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Nome Completo</label>
                            <input type="text" wire:model="name" required class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                            @error('name') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">CPF</label>
                            <input type="text" wire:model="cpf" required maxlength="14" placeholder="000.000.000-00" oninput="maskCpfField(this)" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                            @if(!$user_id)
                                <p class="text-[10px] md:text-[11px] text-zinc-500 mt-2 font-medium">A senha inicial será os 6 primeiros dígitos do CPF.</p>
                            @endif
                            @error('cpf') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">WhatsApp</label>
                            <input type="text" wire:model="whatsapp" maxlength="15" placeholder="(00) 00000-0000" oninput="maskWhatsappField(this)" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                            @error('whatsapp') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Atribuições & Foto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10">
                    <div class="order-2 md:order-1">
                        <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Permissões de Acesso
                        </h3>
                        <div class="flex flex-col gap-3 md:gap-4">
                            <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-xl hover:bg-white/5 border border-transparent hover:border-zinc-800/50 transition-all">
                                <input type="checkbox" wire:model="roles" value="barber" class="w-5 h-5 rounded border border-zinc-600 bg-zinc-900/50 checked:bg-barber-primary checked:border-barber-primary transition-all focus:ring-0 focus:ring-offset-0">
                                <div>
                                    <span class="text-sm md:text-base text-white font-medium block">Barbeiro</span>
                                    <span class="text-xs text-zinc-500">Pode receber agendamentos.</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-4 cursor-pointer group p-3 rounded-xl hover:bg-white/5 border border-transparent hover:border-zinc-800/50 transition-all">
                                <input type="checkbox" wire:model="roles" value="admin" class="w-5 h-5 rounded border border-zinc-600 bg-zinc-900/50 checked:bg-barber-primary checked:border-barber-primary transition-all focus:ring-0 focus:ring-offset-0">
                                <div>
                                    <span class="text-sm md:text-base text-white font-medium block">Administrador</span>
                                    <span class="text-xs text-zinc-500">Acesso total às configurações.</span>
                                </div>
                            </label>
                        </div>
                        @error('roles') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="order-1 md:order-2">
                        <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Foto de Perfil
                        </h3>
                        
                        <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-4 md:gap-6">
                            <label class="relative w-24 h-24 md:w-28 md:h-28 rounded-full border-2 border-dashed border-zinc-700 flex items-center justify-center overflow-hidden bg-zinc-900/50 cursor-pointer hover:border-barber-primary transition-colors group">
                                <input type="file" wire:model="photo" accept="image/*" class="hidden">
                                @if ($photo)
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center text-zinc-500 group-hover:text-barber-primary transition-colors">
                                        <svg class="w-6 h-6 md:w-8 md:h-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                        <span class="text-[9px] md:text-[10px] font-semibold uppercase tracking-wider text-center px-1">Escolher<br>Foto</span>
                                    </div>
                                @endif
                                <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-sm">
                                    <svg class="animate-spin h-6 w-6 text-barber-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </div>
                            </label>

                            @if($photo)
                                <button type="button" wire:click="$set('photo', null)" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors border border-red-500/20 shadow-lg" title="Remover Foto">
                                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            @endif

                            <div class="flex-1 text-center md:text-left mt-2 md:mt-0">
                                <p class="text-[11px] md:text-xs text-zinc-500 font-medium">Formato PNG, JPG até 2MB.</p>
                                @error('photo') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regras de Agendamento e Comissão -->
                <div>
                    <h3 class="text-base md:text-lg font-bold text-white mb-5 md:mb-6 border-b border-zinc-800/50 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-barber-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Financeiro & Expediente
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-6">
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Status da Conta</label>
                            <select wire:model="is_active" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                                <option value="1">Ativo (Pode acessar e receber agenda)</option>
                                <option value="0">Inativo (Bloqueado)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Início Expediente</label>
                            <input type="time" wire:model="work_start_time" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                        </div>
                        
                        <div>
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Fim Expediente</label>
                            <input type="time" wire:model="work_end_time" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Regra de Comissão</label>
                            <select wire:model="commission_type" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium">
                                <option value="percentage">Porcentagem (%)</option>
                                <option value="fixed">Valor Fixo (R$)</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs md:text-sm font-semibold text-zinc-300 mb-2">Valor/Porcentagem</label>
                            <input type="text" wire:model="commission_value" onkeyup="maskCurrency(this)" class="w-full bg-zinc-900/60 border border-zinc-700/50 rounded-xl px-4 py-3 md:py-3.5 text-white focus:outline-none focus:bg-zinc-900/80 focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-all text-sm font-medium text-right">
                            @error('commission_value') <span class="text-red-400 text-xs md:text-sm mt-1.5 block">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <div class="pt-6 md:pt-8 border-t border-zinc-800/50 flex justify-end">
                    <button type="submit" class="w-full md:w-auto px-8 md:px-10 py-3.5 md:py-4 bg-barber-primary text-black font-extrabold uppercase tracking-widest text-sm rounded-xl hover:bg-barber-light transition-all shadow-[0_0_15px_rgba(237,179,41,0.15)] hover:shadow-[0_0_25px_rgba(237,179,41,0.3)] active:scale-95">
                        <span wire:loading.remove wire:target="save">{{ $user_id ? 'Salvar Alterações' : 'Cadastrar Colaborador' }}</span>
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

        function maskWhatsappField(input) {
            let v = input.value.replace(/\D/g, "");
            v = v.substring(0, 11);
            if (v.length > 10) {
                v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
            } else if (v.length > 5) {
                v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
            } else if (v.length > 2) {
                v = v.replace(/^(\d{2})(\d{0,5})/, "($1) $2");
            } else if (v.length > 0) {
                v = v.replace(/^(\d{0,2})/, "($1");
            }
            input.value = v;
        }

        function maskCpfField(input) {
            let v = input.value.replace(/\D/g, "");
            v = v.substring(0, 11);
            if (v.length > 9) {
                v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
            } else if (v.length > 6) {
                v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
            } else if (v.length > 3) {
                v = v.replace(/(\d{3})(\d{1,3})/, "$1.$2");
            }
            input.value = v;
        }
    </script>
</div>
