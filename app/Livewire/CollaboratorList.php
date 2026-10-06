<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CollaboratorList extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
        
        if ($id === auth()->id()) {
            session()->flash('error', 'Você não pode excluir a si mesmo.');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('success', 'Colaborador removido com sucesso!');
    }

    public function render()
    {
        return view('livewire.collaborator-list', [
            'collaborators' => User::whereNotNull('roles')->paginate(10)
        ])->layout('components.layouts.app');
    }
}
