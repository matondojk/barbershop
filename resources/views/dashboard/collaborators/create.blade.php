@extends('layouts.dashboard')

@section('header_title', 'Cadastrar Colaborador')

@section('content')
<div class="max-w-3xl">
    <div class="mb-8">
        <a href="{{ route('collaborators.index') }}" class="text-zinc-500 hover:text-white transition-colors flex items-center gap-2 text-sm uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar
        </a>
    </div>

    <div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl p-8">
        
        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('collaborators.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Nome Completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">CPF</label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" required maxlength="14" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors" oninput="maskCPF(this)">
                    <p class="text-xs text-zinc-500 mt-2">A senha inicial será os 6 primeiros dígitos do CPF.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-zinc-400 mb-2">Atribuição (Cargo)</label>
                    <select name="role" required class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-barber-primary focus:ring-1 focus:ring-barber-primary transition-colors appearance-none">
                        <option value="">Selecione...</option>
                        <option value="barber" {{ old('role') === 'barber' ? 'selected' : '' }}>Barbeiro</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="financial" {{ old('role') === 'financial' ? 'selected' : '' }}>Financeiro</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-400 mb-2">Foto de Perfil</label>
                <input type="file" name="photo" accept="image/*" class="w-full bg-[#0a0a0a]/70 backdrop-blur-md border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-barber-primary hover:file:bg-zinc-700 transition-colors">
            </div>

            <div class="pt-6 border-t border-zinc-800 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
                    Salvar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function maskCPF(input) {
        let v = input.value.replace(/\D/g, "");
        if (v.length > 11) v = v.substring(0, 11);
        if (v.length > 9) {
            v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
        } else if (v.length > 6) {
            v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
        } else if (v.length > 3) {
            v = v.replace(/(\d{3})(\d{1,3})/, "$1.$2");
        }
        input.value = v;
    }
    document.addEventListener('DOMContentLoaded', () => {
        const cpfInput = document.getElementById('cpf');
        if (cpfInput && cpfInput.value) maskCPF(cpfInput);
    });
</script>
@endsection
