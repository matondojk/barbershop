@extends('layouts.dashboard')

@section('header_title', 'Equipe & Colaboradores')

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl text-white font-serif">Membros da Equipe</h2>
    <a href="{{ route('collaborators.create') }}" class="px-6 py-3 bg-barber-primary text-black font-bold uppercase tracking-widest text-sm rounded-lg hover:bg-barber-light transition-all hover:shadow-[0_0_15px_rgba(237,179,41,0.3)]">
        + Novo Colaborador
    </a>
</div>

<div class="bg-[#111]/70 backdrop-blur-md border border-zinc-800 rounded-2xl overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-[#1a1a1a] border-b border-zinc-800 text-zinc-400 uppercase tracking-widest text-xs">
                <th class="p-5 font-medium">Colaborador</th>
                <th class="p-5 font-medium">Atribuição</th>
                <th class="p-5 font-medium">CPF</th>
                <th class="p-5 font-medium">Ações</th>
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
                            <p class="text-zinc-500 text-sm">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="p-5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $user->role === 'admin' ? 'bg-red-500/10 text-red-400 border border-red-500/20' : '' }}
                        {{ $user->role === 'barber' ? 'bg-barber-primary/10 text-barber-primary border border-barber-primary/20' : '' }}
                        {{ $user->role === 'financial' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                    ">
                        @if($user->role === 'admin') Administrador @endif
                        @if($user->role === 'barber') Barbeiro @endif
                        @if($user->role === 'financial') Financeiro @endif
                    </span>
                </td>
                <td class="p-5 text-zinc-400">
                    {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $user->cpf) }}
                </td>
                <td class="p-5">
                    <button class="text-zinc-500 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-8 text-center text-zinc-500">
                    Nenhum colaborador cadastrado ainda.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
