<div>
    @section('header_title', $user_id ? 'Editar Colaborador' : 'Cadastrar Colaborador')

    <div class="max-w-3xl">
        <div class="mb-8">
            <a wire:navigate href="{{ route('collaborators.index') }}" class="text-zinc-500 hover:text-white transition-colors flex items-center gap-2 text-sm uppercase tracking-widest">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Voltar
            </a>
        </div>

        <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-8">
            <h3 class="text-xl text-white font-serif mb-6">{{ $user_id ? 'Editar Informações' : 'Informações do Colaborador' }}</h3>
            
            <form wire:submit="save" class="space-y-6">
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Nome Completo</label>
                        <input type="text" wire:model="name" required class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                        @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">CPF</label>
                        <input type="text" wire:model="cpf" required maxlength="14" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                        <p class="text-xs text-zinc-500 mt-2">A senha inicial será os 6 primeiros dígitos do CPF.</p>
                        @error('cpf') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Atribuições (Selecione um ou mais)</label>
                        <div class="flex flex-col gap-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model="roles" value="barber" class="w-5 h-5 rounded border-zinc-800 bg-[#0a0a0a]/70 backdrop-blur-md text-barber-primary focus:ring-barber-primary focus:ring-offset-[#111]">
                                <span class="text-white group-hover:text-barber-primary transition-colors">Barbeiro</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model="roles" value="admin" class="w-5 h-5 rounded border-zinc-800 bg-[#0a0a0a]/70 backdrop-blur-md text-barber-primary focus:ring-barber-primary focus:ring-offset-[#111]">
                                <span class="text-white group-hover:text-barber-primary transition-colors">Administrador</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" wire:model="roles" value="financial" class="w-5 h-5 rounded border-zinc-800 bg-[#0a0a0a]/70 backdrop-blur-md text-barber-primary focus:ring-barber-primary focus:ring-offset-[#111]">
                                <span class="text-white group-hover:text-barber-primary transition-colors">Financeiro</span>
                            </label>
                        </div>
                        @error('roles') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-zinc-800">
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Status</label>
                        <select wire:model="is_active" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary appearance-none">
                            <option value="1">Ativo (Pode receber agenda)</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Início Expediente</label>
                        <input type="time" wire:model="work_start_time" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary custom-calendar-icon">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Fim Expediente</label>
                        <input type="time" wire:model="work_end_time" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary custom-calendar-icon">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-zinc-800">
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Regra de Comissão</label>
                        <select wire:model="commission_type" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary appearance-none">
                            <option value="percentage">Porcentagem (%)</option>
                            <option value="fixed">Valor Fixo (R$)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-2">Valor/Porcentagem</label>
                        <input type="text" wire:model="commission_value" onkeyup="maskCurrency(this)" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                        @error('commission_value') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-6 border-t border-zinc-800">
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Foto de Perfil</label>
                    <input type="file" wire:model="photo" accept="image/*" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-barber-primary hover:file:bg-zinc-700 transition-colors">
                    @error('photo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    
                    <div wire:loading wire:target="photo" class="text-barber-primary text-sm mt-2">Carregando imagem...</div>
                    
                    @if ($photo)
                        <div class="mt-4">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-24 h-24 rounded-full object-cover border-2 border-barber-primary">
                        </div>
                    @endif
                </div>

                <div class="pt-6 border-t border-zinc-800 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
                        <span wire:loading.remove wire:target="save">{{ $user_id ? 'Salvar Alterações' : 'Salvar Colaborador' }}</span>
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
