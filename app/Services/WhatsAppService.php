<?php

namespace App\Services;

use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Cache;
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

        $activeInstance = WhatsappInstance::getActiveInstanceName();
        $this->instance = $activeInstance ?: config('services.evolution.instance', env('EVOLUTION_INSTANCE', 'Teste'));
    }

    public function sendMessage(string $phone, string $message, ?string $instance = null): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('Evolution API Key not configured.');

            return false;
        }

        $targetInstance = $instance ?: $this->instance;

        if (empty($targetInstance)) {
            Log::warning('Nenhuma instância ativa do WhatsApp configurada.');

            return false;
        }

        // Clean phone number (leave only digits)
        $number = preg_replace('/\D/', '', $phone);

        // Ensure it has country code, default to Brazil if 10 or 11 digits
        if (strlen($number) === 10 || strlen($number) === 11) {
            $number = '55'.$number;
        }

        try {
            Log::info("Enviando WhatsApp para {$number} usando instância {$targetInstance}: \n{$message}");

            // Store message text hash in cache to identify bot self-replies
            Cache::put('bot_sent_hash_'.md5(trim($message)), true, now()->addMinutes(2));

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/message/sendText/{$targetInstance}", [
                'number' => $number,
                'text' => $message,
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $msgId = $resData['key']['id'] ?? $resData['id'] ?? null;
                if ($msgId) {
                    Cache::put("bot_sent_msg_id_{$msgId}", true, now()->addMinutes(10));
                }

                return true;
            }

            Log::error('Failed to send WhatsApp message: '.$response->body());

            return false;

        } catch (\Exception $e) {
            Log::error('Exception when sending WhatsApp message: '.$e->getMessage());

            return false;
        }
    }
}
