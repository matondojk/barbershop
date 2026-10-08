<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceList extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_name = '';
    public $filter_price_min = '';
    public $filter_price_max = '';
    public $filter_duration = '';

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filter_name = '';
        $this->filter_price_min = '';
        $this->filter_price_max = '';
        $this->filter_duration = '';
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter_name', 'filter_price_min', 'filter_price_max', 'filter_duration'])) {
            $this->resetPage();
        }
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
        Service::findOrFail($id)->delete();
        session()->flash('success', 'Serviço removido com sucesso!');
    }

    public function render()
    {
        $query = Service::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filter_name)) {
            $query->where('name', 'like', '%' . $this->filter_name . '%');
        }
        if (!empty($this->filter_price_min)) {
            $query->where('price', '>=', $this->filter_price_min);
        }
        if (!empty($this->filter_price_max)) {
            $query->where('price', '<=', $this->filter_price_max);
        }
        if (!empty($this->filter_duration)) {
            $query->where('duration_minutes', '=', $this->filter_duration);
        }

        return view('livewire.service-list', [
            'services' => $query->orderBy('id', 'desc')->paginate(10)
        ])->layout('components.layouts.app');
    }
}
