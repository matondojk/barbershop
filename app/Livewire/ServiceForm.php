<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;

class ServiceForm extends Component
{
    public $service_id;
    public $name;
    public $description;
    public $price;
    public $duration_minutes;
    public $is_active = true;

    public function mount($service = null)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso não autorizado.');

        if ($service) {
            $svc = Service::findOrFail($service);
            $this->service_id = $svc->id;
            $this->name = $svc->name;
            $this->description = $svc->description;
            $this->price = number_format($svc->price, 2, ',', '.');
            $this->duration_minutes = $svc->duration_minutes;
            $this->is_active = $svc->is_active;
        }
    }

    public function save()
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        // Convert formatted currency back to float for validation and DB
        $cleanPrice = str_replace(['.', ','], ['', '.'], $this->price);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if (!is_numeric($cleanPrice) || $cleanPrice < 0) {
            $this->addError('price', 'O preço informado é inválido.');
            return;
        }

        if ($this->service_id) {
            $svc = Service::findOrFail($this->service_id);
            $svc->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $cleanPrice,
                'duration_minutes' => $this->duration_minutes,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Corte atualizado com sucesso!');
        } else {
            Service::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $cleanPrice,
                'duration_minutes' => $this->duration_minutes,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Corte cadastrado com sucesso!');
        }

        return redirect()->route('services.index');
    }

    public function render()
    {
        return view('livewire.service-form')->layout('components.layouts.app');
    }
}
