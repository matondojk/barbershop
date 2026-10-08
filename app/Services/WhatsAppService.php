<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->apiUrl = config('services.evolution.url', env('EVOLUTION_API_URL', 'http://host.docker.internal:8080'));
        $this->apiKey = config('services.evolution.key', env('EVOLUTION_API_KEY'));
        
        $activeInstance = \App\Models\WhatsappInstance::getActiveInstanceName();
        $this->instance = $activeInstance ?: config('services.evolution.instance', env('EVOLUTION_INSTANCE', 'Teste'));
    }

    public function sendMessage(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning("Evolution API Key not configured.");
            return false;
        }

        if (empty($this->instance)) {
            Log::warning("Nenhuma instância ativa do WhatsApp configurada.");
            return false;
        }

        // Clean phone number (leave only digits)
        $number = preg_replace('/\D/', '', $phone);
        
        // Ensure it has country code, default to Brazil if 10 or 11 digits
        if (strlen($number) === 10 || strlen($number) === 11) {
            $number = '55' . $number;
        }

        try {
            Log::info("Enviando WhatsApp para {$number} usando instância {$this->instance}: \n{$message}");

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post("{$this->apiUrl}/message/sendText/{$this->instance}", [
                'number' => $number,
                'text' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error("Failed to send WhatsApp message: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("Exception when sending WhatsApp message: " . $e->getMessage());
            return false;
        }
    }
}
