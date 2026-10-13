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

        // Handle potential nested structure if data is wrapped in messages array
        if (isset($data['messages']) && is_array($data['messages']) && ! empty($data['messages'])) {
            $data = $data['messages'][0];
        }

        $key = $data['key'] ?? [];
        $fromMe = $key['fromMe'] ?? false;
        $remoteJid = $key['remoteJid'] ?? '';

        // Ignore messages sent by the bot itself
        if ($fromMe) {
            return response()->json(['status' => 'ignored_from_me']);
        }

        // Ignore groups or status updates
        if (str_contains($remoteJid, '@g.us') || str_contains($remoteJid, 'status@broadcast')) {
            return response()->json(['status' => 'ignored_group_or_broadcast']);
        }

        $phone = explode('@', $remoteJid)[0];
        if (empty($phone)) {
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
            ?? '';

        $pushName = $data['pushName'] ?? 'Cliente';

        if (empty(trim($text))) {
            return response()->json(['status' => 'ignored_empty_text']);
        }

        Log::info("WhatsApp Bot processando mensagem de {$phone} ({$pushName}): {$text}");

        try {
            $this->botService->processMessage($phone, $text, $pushName);
        } catch (\Throwable $e) {
            Log::error('Erro no processamento do bot WhatsApp: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'processed']);
    }
}
