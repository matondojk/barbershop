<div>
    @section('header_title', 'Meu Perfil')

    <div class="max-w-3xl mx-auto">
        <div class="bg-[#111] border border-zinc-800 rounded-2xl p-8">
            <h3 class="text-xl text-white font-serif mb-6">Editar Informações</h3>
            
            <form wire:submit="save" class="space-y-6">
                <!-- CPF Field (Readonly) -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Seu CPF (Não pode ser alterado)</label>
                    <input type="text" value="{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', auth()->user()->cpf) }}" readonly class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-zinc-600 cursor-not-allowed">
                </div>

                <!-- Name Field -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Nome Completo</label>
                    <input type="text" wire:model="name" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary transition-colors">
                    @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Photo Field -->
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Foto de Perfil</label>
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-full bg-zinc-800 border border-zinc-700 overflow-hidden flex items-center justify-center shrink-0">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                            @elseif(auth()->user()->photo_path)
                                <img src="{{ Storage::url(auth()->user()->photo_path) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-2xl font-bold text-zinc-500">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" wire:model="photo" accept="image/*" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-barber-primary hover:file:bg-zinc-700 transition-colors">
                        </div>
                    </div>
                    @error('photo') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password Field -->
                <div class="pt-6 border-t border-zinc-800">
                    <h4 class="text-lg text-white font-medium mb-4">Alterar Senha</h4>
                    <p class="text-sm text-zinc-500 mb-4">Deixe em branco se não quiser alterar sua senha.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Nova Senha</label>
                            <input type="password" wire:model="password" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary transition-colors">
                            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-2">Confirmar Nova Senha</label>
                            <input type="password" wire:model="password_confirmation" class="w-full bg-[#0a0a0a] border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-zinc-800 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
                        <span wire:loading.remove wire:target="save">Salvar Alterações</span>
                        <span wire:loading wire:target="save">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
