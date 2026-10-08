<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CollaboratorList extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_name = '';
    public $filter_cpf = '';
    public $filter_whatsapp = '';
    public $filter_work_start_time = '';
    public $filter_work_end_time = '';

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'filter_name', 'filter_cpf', 'filter_whatsapp', 'filter_work_start_time', 'filter_work_end_time'])) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filter_name', 'filter_cpf', 'filter_whatsapp', 'filter_work_start_time', 'filter_work_end_time']);
        $this->resetPage();
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
        $query = User::whereNotNull('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('cpf', 'like', '%' . $this->search . '%')
                  ->orWhere('whatsapp', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filter_name) {
            $query->where('name', 'like', '%' . $this->filter_name . '%');
        }
        if ($this->filter_cpf) {
            $query->where('cpf', 'like', '%' . $this->filter_cpf . '%');
        }
        if ($this->filter_whatsapp) {
            $query->where('whatsapp', 'like', '%' . $this->filter_whatsapp . '%');
        }
        if ($this->filter_work_start_time) {
            $query->where('work_start_time', '>=', $this->filter_work_start_time);
        }
        if ($this->filter_work_end_time) {
            $query->where('work_end_time', '<=', $this->filter_work_end_time);
        }

        return view('livewire.collaborator-list', [
            'collaborators' => $query->paginate(10)
        ])->layout('components.layouts.app');
    }
}
