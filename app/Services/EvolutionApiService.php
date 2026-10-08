<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        // Padrão para a porta 8090 que configuramos anteriormente
        $this->baseUrl = env('EVOLUTION_API_URL', 'http://localhost:8090');
        $this->apiKey = env('EVOLUTION_API_KEY', 'E0D7B246-A4C3-4859-906D-1E7C4B25164B');
    }

    protected function request()
    {
        return Http::withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function createInstance(string $name)
    {
        $response = $this->request()->post("{$this->baseUrl}/instance/create", [
            'instanceName' => $name,
            'integration' => 'WHATSAPP-BAILEYS',
        ])->throw();

        return $response->json();
    }

    public function deleteInstance(string $instanceId)
    {
        $response = $this->request()->delete("{$this->baseUrl}/instance/delete/{$instanceId}")->throw();
        return $response->json();
    }

    public function getAllInstances()
    {
        $response = $this->request()->get("{$this->baseUrl}/instance/fetchInstances")->throw();
        return $response->json();
    }

    public function connectInstance(string $name)
    {
        $response = $this->request()->get("{$this->baseUrl}/instance/connect/{$name}")->throw();
        return $response->json();
    }

    public function getInstanceStatus(string $name)
    {
        $response = $this->request()->get("{$this->baseUrl}/instance/connectionState/{$name}")->throw();
        return $response->json();
    }
}
