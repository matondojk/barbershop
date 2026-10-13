<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $messageId = $key['id'] ?? $data['id'] ?? null;
        $fromMe = $key['fromMe'] ?? $data['fromMe'] ?? false;
        $remoteJid = $key['remoteJid'] ?? $data['remoteJid'] ?? $payload['sender'] ?? '';

        Log::info('Webhook Message Parsed: ', [
            'fromMe' => $fromMe,
            'messageId' => $messageId,
            'remoteJid' => $remoteJid,
            'sender' => $payload['sender'] ?? null,
            'destination' => $payload['destination'] ?? null,
        ]);

        // 1. Check if this exact message ID was sent by our bot API
        if ($messageId && Cache::has("bot_sent_msg_id_{$messageId}")) {
            Log::info("Webhook ignorado: ID gerado pelo bot ({$messageId})");

            return response()->json(['status' => 'ignored_bot_api_message']);
        }

        // 2. Ignore groups or status updates
        if (str_contains($remoteJid, '@g.us') || str_contains($remoteJid, 'status@broadcast')) {
            Log::info("Webhook ignorado: grupo ou broadcast ({$remoteJid})");

            return response()->json(['status' => 'ignored_group_or_broadcast']);
        }

        // 3. Extract text
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

        if (empty(trim($text))) {
            Log::warning('Webhook ignorado: texto vazio', [
                'messageObj' => $messageObj,
                'data_keys' => array_keys($data),
            ]);

            return response()->json(['status' => 'ignored_empty_text']);
        }

        // 4. Check if this text hash was recently sent by the bot (prevents loop)
        $textHash = md5(trim($text));
        if (Cache::has("bot_sent_hash_{$textHash}")) {
            Log::info('Webhook ignorado: hash de resposta do bot');

            return response()->json(['status' => 'ignored_bot_text_hash']);
        }

        // 5. Determine the phone number
        $candidateSources = [
            $payload['sender'] ?? '',
            $payload['destination'] ?? '',
            $data['participant'] ?? '',
            $data['key']['participant'] ?? '',
            $remoteJid,
        ];

        $phone = '';
        foreach ($candidateSources as $source) {
            if (! empty($source) && ! str_contains($source, '@g.us') && ! str_contains($source, 'status@broadcast')) {
                // If not @lid, extract phone
                if (! str_contains($source, '@lid')) {
                    $cleaned = preg_replace('/\D/', '', explode('@', $source)[0]);
                    if (strlen($cleaned) >= 10) {
                        $phone = $cleaned;
                        break;
                    }
                }
            }
        }

        // If only @lid exists, extract digits or user phone
        if (empty($phone)) {
            $cleaned = preg_replace('/\D/', '', explode('@', $remoteJid)[0]);
            $phone = $cleaned ?: explode('@', $remoteJid)[0];
        }

        if (empty($phone)) {
            Log::warning("Webhook ignorado: telefone vazio. remoteJid: {$remoteJid}");

            return response()->json(['status' => 'ignored_no_phone']);
        }

        if ($fromMe) {
            Log::info("Processando mensagem enviada pelo próprio número ({$phone}) para fins de teste.");
        }

        $pushName = $data['pushName'] ?? 'Cliente';
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
