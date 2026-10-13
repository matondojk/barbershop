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
        $remoteJid = $key['remoteJid'] ?? $data['remoteJid'] ?? '';

        Log::info('Evolution Data Raw: ', [
            'data_keys' => array_keys($data),
            'key_keys' => array_keys($key),
            'data' => $data,
        ]);

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

        $pushName = $data['pushName'] ?? 'Cliente';

        // 5. Determine the contact's phone number
        $phone = '';

        if ($fromMe) {
            // The bot owner is testing from their own connected device
            $ownerJid = $payload['sender'] ?? '';
            $phone = preg_replace('/\D/', '', explode('@', $ownerJid)[0]);
            Log::info("Processando mensagem enviada pelo próprio número ({$phone}) para fins de teste.");
        } else {
            // Message from an external contact (friend, customer, etc.)
            if (str_contains($remoteJid, '@s.whatsapp.net')) {
                $phone = preg_replace('/\D/', '', explode('@', $remoteJid)[0]);
            } elseif (str_contains($remoteJid, '@lid')) {
                // Resolve LID to the real phone number using Evolution API's PostgreSQL
                $phone = $this->resolveLidToPhone($remoteJid, $pushName);
            }

            // Fallback: check other fields if still empty
            if (empty($phone)) {
                $cand = $data['participant'] ?? $data['key']['participant'] ?? '';
                if (! empty($cand) && ! str_contains($cand, '@lid')) {
                    $phone = preg_replace('/\D/', '', explode('@', $cand)[0]);
                }
            }
        }

        if (empty($phone)) {
            Log::warning("Webhook ignorado: não foi possível identificar o telefone do contato. remoteJid: {$remoteJid}");

            return response()->json(['status' => 'ignored_no_phone']);
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

    /**
     * Resolve a WhatsApp LID to a phone number using Evolution API's PostgreSQL
     */
    protected function resolveLidToPhone(string $lid, string $pushName = ''): ?string
    {
        try {
            $pdo = new \PDO('pgsql:host=host.docker.internal;port=5432;dbname=evolution', 'evolution', 'evolution', [
                \PDO::ATTR_TIMEOUT => 2,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $stmt = $pdo->prepare('
                SELECT c2."remoteJid" 
                FROM "Contact" c1
                JOIN "Contact" c2 ON (
                    (c1."profilePicUrl" IS NOT NULL AND c1."profilePicUrl" = c2."profilePicUrl")
                    OR (c1."pushName" IS NOT NULL AND c1."pushName" = c2."pushName")
                )
                WHERE c1."remoteJid" = :lid
                  AND c2."remoteJid" LIKE \'%@s.whatsapp.net\'
                LIMIT 1
            ');
            $stmt->execute([':lid' => $lid]);
            $realJid = $stmt->fetchColumn();

            if ($realJid) {
                return explode('@', $realJid)[0];
            }

            // Fallback: search by pushName
            if (! empty($pushName) && $pushName !== 'Cliente') {
                $stmt = $pdo->prepare('
                    SELECT "remoteJid" FROM "Contact" 
                    WHERE "pushName" = :name AND "remoteJid" LIKE \'%@s.whatsapp.net\'
                    LIMIT 1
                ');
                $stmt->execute([':name' => $pushName]);
                $realJid = $stmt->fetchColumn();
                if ($realJid) {
                    return explode('@', $realJid)[0];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Não foi possível resolver LID via banco Evolution: '.$e->getMessage());
        }

        return null;
    }
}
