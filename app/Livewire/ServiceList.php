<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Corte removido com sucesso!');
    }

    public function render()
    {
        return view('livewire.service-list', [
            'services' => Service::paginate(10)
        ])->layout('components.layouts.app');
    }
}
