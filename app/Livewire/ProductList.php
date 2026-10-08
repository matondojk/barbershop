<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';

    public $filter_name = '';

    public $filter_price_min = '';

    public $filter_price_max = '';

    public $filter_status = '';

    public function mount()
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filter_name = '';
        $this->filter_price_min = '';
        $this->filter_price_max = '';
        $this->filter_status = '';
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter_name', 'filter_price_min', 'filter_price_max', 'filter_status'])) {
            $this->resetPage();
        }
    }

    public function delete($id)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Produto removido com sucesso!');
    }

    public function toggleStatus($id)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');
        $product = Product::findOrFail($id);
        $product->is_active = ! $product->is_active;
        $product->save();
        session()->flash('success', 'Status do produto atualizado!');
    }

    public function render()
    {
        $query = Product::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->filter_name)) {
            $query->where('name', 'like', '%'.$this->filter_name.'%');
        }
        if (! empty($this->filter_price_min)) {
            $query->where('price', '>=', $this->filter_price_min);
        }
        if (! empty($this->filter_price_max)) {
            $query->where('price', '<=', $this->filter_price_max);
        }
        if ($this->filter_status !== '') {
            $query->where('is_active', $this->filter_status === '1');
        }

        return view('livewire.product-list', [
            'products' => $query->orderBy('id', 'desc')->paginate(10),
        ])->layout('components.layouts.app');
    }
}
