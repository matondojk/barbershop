<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected WhatsAppBotService $botService
    ) {}

    /**
     * Handle incoming webhook from Evolution API.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        $event = $payload['event'] ?? $payload['type'] ?? '';

        Log::info("Evolution API Webhook recebido: {$event}", [
            'event' => $event,
            'instance' => $payload['instance'] ?? 'unknown',
        ]);

        // Only handle message upsert events
        if (! in_array(mb_strtolower($event), ['messages.upsert', 'messages_upsert'])) {
            return response()->json([
                'status' => 'ignored',
                'message' => "Event {$event} ignored",
            ]);
        }

        $data = $payload['data'] ?? [];

        Log::info('Evolution Webhook Payload Data: ', [
            'raw_keys' => array_keys($payload),
            'data_is_array' => is_array($data),
            'data_type' => gettype($data),
        ]);

        // If data is a numeric array of messages (common in Baileys / Evolution v2)
        if (is_array($data) && isset($data[0])) {
            $data = $data[0];
        }

        // Handle nested messages array
        if (isset($data['messages']) && is_array($data['messages']) && ! empty($data['messages'])) {
            $data = $data['messages'][0];
        }

        $key = $data['key'] ?? [];
        $fromMe = $key['fromMe'] ?? $data['fromMe'] ?? false;
        $remoteJid = $key['remoteJid'] ?? $data['remoteJid'] ?? $payload['sender'] ?? '';

        Log::info('Webhook Message Parsed: ', [
            'fromMe' => $fromMe,
            'remoteJid' => $remoteJid,
        ]);

        // Ignore messages sent by the bot itself
        if ($fromMe) {
            Log::info('Webhook ignorado: fromMe = true');

            return response()->json(['status' => 'ignored_from_me']);
        }

        // Ignore groups or status updates
        if (str_contains($remoteJid, '@g.us') || str_contains($remoteJid, 'status@broadcast')) {
            Log::info("Webhook ignorado: grupo ou broadcast ({$remoteJid})");

            return response()->json(['status' => 'ignored_group_or_broadcast']);
        }

        $phone = explode('@', $remoteJid)[0];
        if (empty($phone)) {
            Log::warning("Webhook ignorado: telefone vazio. remoteJid: {$remoteJid}");

            return response()->json(['status' => 'ignored_no_phone']);
        }

        // Extract message text from different Evolution API formats
        $messageObj = $data['message'] ?? [];
        $text = $messageObj['conversation']
            ?? $messageObj['extendedTextMessage']['text']
            ?? $messageObj['buttonsResponseMessage']['selectedDisplayText']
            ?? $messageObj['buttonsResponseMessage']['selectedButtonId']
            ?? $messageObj['listResponseMessage']['title']
            ?? $messageObj['listResponseMessage']['singleSelectReply']['selectedRowId']
            ?? $data['body']
            ?? $data['text']
            ?? '';

        $pushName = $data['pushName'] ?? 'Cliente';

        if (empty(trim($text))) {
            Log::warning('Webhook ignorado: texto vazio', [
                'messageObj' => $messageObj,
                'data_keys' => array_keys($data),
            ]);

            return response()->json(['status' => 'ignored_empty_text']);
        }

        $instance = $payload['instance'] ?? 'wp_barber';
        Log::info("WhatsApp Bot processando mensagem de {$phone} ({$pushName}) na instância {$instance}: {$text}");

        try {
            $this->botService->processMessage($phone, $text, $pushName, $instance);
        } catch (\Throwable $e) {
            Log::error('Erro no processamento do bot WhatsApp: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'processed']);
    }
}
