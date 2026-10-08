<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductForm extends Component
{
    public $product_id;

    public $name;

    public $price;

    public $stock_quantity;

    public $is_active = true;

    public function mount($product = null)
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');

        if ($product) {
            $prod = Product::findOrFail($product);
            $this->product_id = $prod->id;
            $this->name = $prod->name;
            $this->price = number_format($prod->price, 2, ',', '.');
            $this->stock_quantity = $prod->stock_quantity;
            $this->is_active = $prod->is_active;
        }
    }

    public function save()
    {
        abort_if(! auth()->user()->isAdmin(), 403);

        // Convert formatted currency back to float for validation and DB
        $cleanPrice = str_replace(['.', ','], ['', '.'], $this->price);

        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'stock_quantity' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (! is_numeric($cleanPrice) || $cleanPrice < 0) {
            $this->addError('price', 'O preço informado é inválido.');

            return;
        }

        if ($this->product_id) {
            $prod = Product::findOrFail($this->product_id);
            $prod->update([
                'name' => $this->name,
                'price' => $cleanPrice,
                'stock_quantity' => $this->stock_quantity,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Produto atualizado com sucesso!');
        } else {
            Product::create([
                'name' => $this->name,
                'price' => $cleanPrice,
                'stock_quantity' => $this->stock_quantity,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Produto cadastrado com sucesso!');
        }

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.product-form')->layout('components.layouts.app');
    }
}
