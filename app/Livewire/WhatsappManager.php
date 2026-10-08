<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;

class WhatsappManager extends Component
{
    public $instances = [];
    public $qrCode = null;
    public $currentInstance = null;
    public $status = '';

    public function mount()
    {
        $this->loadInstances();
    }

    public function loadInstances()
    {
        $this->instances = WhatsappInstance::all();
    }

    public function deleteInstance($id, EvolutionApiService $api)
    {
        $instance = WhatsappInstance::find($id);
        if ($instance) {
            try {
                $api->deleteInstance($instance->name);
            } catch (\Exception $e) {
                // Ignore se não existir na API
            }
            $instance->delete();
            $this->loadInstances();
        }
    }

    public function activateInstance($id)
    {
        WhatsappInstance::query()->update(['is_active' => false]);
        WhatsappInstance::where('id', $id)->update(['is_active' => true]);
        $this->loadInstances();
    }

    public function connect($name, EvolutionApiService $api)
    {
        $this->currentInstance = $name;
        $this->qrCode = null;
        $this->status = 'connecting';
        
        try {
            $response = $api->connectInstance($name);
            
            if (isset($response['qrcode'])) {
                $this->qrCode = $response['qrcode'];
            } elseif (isset($response['data']['qrcode'])) {
                $this->qrCode = $response['data']['qrcode'];
            } elseif (isset($response['base64'])) {
                $this->qrCode = $response['base64'];
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao obter QR Code: ' . $e->getMessage());
        }
    }

    public function checkStatus(EvolutionApiService $api)
    {
        if (!$this->currentInstance) return;

        try {
            $response = $api->getInstanceStatus($this->currentInstance);
            
            $connected = false;
            if (isset($response['data']['Connected'])) {
                $connected = $response['data']['Connected'];
            } elseif (isset($response['instance']['state']) && $response['instance']['state'] === 'open') {
                $connected = true;
            }

            if ($connected) {
                $this->status = 'connected';
                $this->qrCode = null;
            }
        } catch (\Exception $e) {
            // Silently ignore poll errors
        }
    }

    public function render()
    {
        return view('livewire.whatsapp-manager')->layout('components.layouts.app');
    }
}
