<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;

class WhatsappCreate extends Component
{
    public $name = '';
    public $description = '';

    public function createInstance(EvolutionApiService $api)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        
        try {
            $api->createInstance($this->name);
            
            WhatsappInstance::create([
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => WhatsappInstance::count() === 0
            ]);

            session()->flash('message', 'Instância criada com sucesso!');
            return redirect()->route('whatsapp.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar instância na API: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.whatsapp-create')->layout('components.layouts.app');
    }
}
