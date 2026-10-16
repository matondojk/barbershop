<?php

namespace App\Services;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Notifications\NewAppointmentNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppBotService
{
    protected ?string $currentInstance = null;

    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    protected function reply(string $phone, string $message): bool
    {
        return $this->whatsAppService->sendMessage($phone, $message, $this->currentInstance);
    }

    /**
     * Process an incoming message from WhatsApp Webhook.
     */
    public function processMessage(string $phone, string $text, string $pushName = 'Cliente', ?string $instance = null): void
    {
        $this->currentInstance = $instance;
        $cleanText = trim($text);
        $lowerText = mb_strtolower($cleanText);

        // Normalize phone for caching
        $phoneKey = preg_replace('/\D/', '', $phone);
        $cacheKey = "whatsapp_bot_session_{$phoneKey}";

        // Global command to reset to main menu (match any variations of greeting or menu)
        if (preg_match('/^(oi+|ol[aá]+|bom\s*dia|boa\s*tarde|boa\s*noite|menu|in[ií]cio|come[cç]ar|ajuda|cancelar|sair|voltar|reset|help|opa|fala|e\s*a[ií])/i', $lowerText)) {
            Cache::forget($cacheKey);
            $this->sendMainMenu($phone, $pushName);

            return;
        }

        $session = Cache::get($cacheKey, [
            'step' => 'MENU',
            'pushName' => $pushName,
        ]);

        switch ($session['step'] ?? 'MENU') {
            case 'MENU':
                $this->handleMenu($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CHOOSING_SERVICE':
                $this->handleChoosingService($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CHOOSING_BARBER':
                $this->handleChoosingBarber($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CHOOSING_DATE':
                $this->handleChoosingDate($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CHOOSING_TIME':
                $this->handleChoosingTime($phone, $cleanText, $session, $cacheKey);
                break;

            case 'ASKING_NAME':
                $this->handleAskingName($phone, $cleanText, $session, $cacheKey);
                break;

            case 'ASKING_CPF':
                $this->handleAskingCpf($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CONFIRMING':
                $this->handleConfirming($phone, $cleanText, $session, $cacheKey);
                break;

            case 'SELECTING_APPT':
                $this->handleSelectingAppt($phone, $cleanText, $session, $cacheKey);
                break;

            case 'MANAGING_APPT':
                $this->handleManagingAppt($phone, $cleanText, $session, $cacheKey);
                break;

            case 'CONFIRM_CANCEL_APPT':
                $this->handleConfirmCancelAppt($phone, $cleanText, $session, $cacheKey);
                break;

            default:
                Cache::forget($cacheKey);
                $this->sendMainMenu($phone, $pushName);
                break;
        }
    }

    /**
     * Send Main Menu
     */
    protected function sendMainMenu(string $phone, string $name = 'Cliente'): void
    {
        $cacheKey = 'whatsapp_bot_session_'.preg_replace('/\D/', '', $phone);
        Cache::put($cacheKey, [
            'step' => 'MENU',
            'pushName' => $name,
        ], now()->addHours(2));

        $saudacao = $this->getGreeting();
        $msg = "💈 *Barbearia Barcelona* 💈\n\n"
             ."{$saudacao}, *{$name}*! Seja muito bem-vindo(a) ao nosso atendimento virtual.\n\n"
             ."Como posso te ajudar hoje? Envie o *número* da opção desejada:\n\n"
             ."1️⃣ *Agendar Horário*\n"
             ."2️⃣ *Meus Agendamentos (Ver, Alterar ou Cancelar)*\n"
             ."3️⃣ *Ver Serviços e Valores*\n"
             ."4️⃣ *Endereço e Contato*\n\n"
             .'_(Dica: você pode digitar *menu* a qualquer momento para voltar aqui)_';

        $this->reply($phone, $msg);
    }

    protected function handleMenu(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '1') {
            unset($session['editing_appointment_id'], $session['edit_mode']);
            $this->startScheduling($phone, $session, $cacheKey);
        } elseif ($input === '2') {
            $this->showCustomerAppointments($phone, $session, $cacheKey);
        } elseif ($input === '3') {
            $this->showServicesList($phone);
        } elseif ($input === '4') {
            $this->showAboutAndLocation($phone);
        } else {
            $this->sendMainMenu($phone, $session['pushName'] ?? 'Cliente');
        }
    }

    /**
     * Step 1: Start Scheduling - List Services
     */
    protected function startScheduling(string $phone, array $session, string $cacheKey): void
    {
        $services = Service::where('is_active', true)->orderBy('price', 'asc')->get();

        if ($services->isEmpty()) {
            $this->reply($phone, 'No momento não há serviços disponíveis para agendamento. Por favor, tente novamente mais tarde.');

            return;
        }

        $isEditing = ! empty($session['editing_appointment_id']);
        $currentServiceId = $session['service_id'] ?? null;

        $options = [];
        $text = "✂️ *Escolha o serviço desejado:*\n\n";

        $num = 1;
        foreach ($services as $srv) {
            $options[$num] = $srv->id;
            $formattedPrice = number_format($srv->price, 2, ',', '.');
            $currentTag = ($isEditing && (int) $currentServiceId === (int) $srv->id) ? ' *(Atual)*' : '';
            $text .= "{$num} - *{$srv->name}* (R$ {$formattedPrice} - {$srv->duration_minutes} min){$currentTag}\n";
            $num++;
        }

        $backLabel = $isEditing ? 'Voltar ao gerenciamento do agendamento' : 'Voltar ao menu principal';
        $text .= "\n0 - 🔙 {$backLabel}\n\n";
        $text .= 'Envie o *número* do serviço:';

        $session['step'] = 'CHOOSING_SERVICE';
        $session['service_options'] = $options;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply($phone, $text);
    }

    protected function handleChoosingService(string $phone, string $input, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);

        if ($input === '0') {
            if ($isEditing) {
                $this->returnToManageAppointment($phone, $session, $cacheKey);
            } else {
                $this->sendMainMenu($phone, $session['pushName'] ?? 'Cliente');
            }

            return;
        }

        $serviceOptions = $session['service_options'] ?? [];
        if (! isset($serviceOptions[$input])) {
            $this->reply($phone, 'Número inválido. Por favor, digite o número correspondente a um dos serviços listados acima (ou *0* para voltar).');

            return;
        }

        $serviceId = $serviceOptions[$input];
        $service = Service::find($serviceId);

        if (! $service) {
            $this->startScheduling($phone, $session, $cacheKey);

            return;
        }

        $session['service_id'] = $serviceId;

        if ($isEditing && ($session['edit_mode'] ?? '') === 'service') {
            // Check available slots on the current appointment date & barber for the new service duration
            $this->promptChoosingTimeForDate($phone, $session, $cacheKey, $session['appointment_date']);

            return;
        }

        $this->promptChoosingBarber($phone, $session, $cacheKey);
    }

    protected function promptChoosingBarber(string $phone, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);
        $currentBarberId = $session['barber_id'] ?? null;

        // Load active barbers
        $barbers = User::whereNotNull('roles')
            ->where('is_active', true)
            ->get()
            ->filter(fn ($u) => in_array('barber', $u->roles ?? []));

        $barberOptions = [];
        $text = "💈 *Escolha o Barbeiro:*\n\n";
        $text .= "1 - ✂️ *Qualquer Barbeiro Disponível*\n";
        $barberOptions[1] = 'any';

        $idx = 2;
        foreach ($barbers as $barber) {
            $barberOptions[$idx] = $barber->id;
            $currentTag = ($isEditing && (int) $currentBarberId === (int) $barber->id) ? ' *(Atual)*' : '';
            $text .= "{$idx} - {$barber->name}{$currentTag}\n";
            $idx++;
        }

        $text .= "\n0 - 🔙 Voltar\n\n";
        $text .= 'Envie o *número* do profissional:';

        $session['step'] = 'CHOOSING_BARBER';
        $session['barber_options'] = $barberOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply($phone, $text);
    }

    protected function handleChoosingBarber(string $phone, string $input, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);

        if ($input === '0') {
            if ($isEditing && ($session['edit_mode'] ?? '') === 'barber') {
                $this->returnToManageAppointment($phone, $session, $cacheKey);
            } else {
                $this->startScheduling($phone, $session, $cacheKey);
            }

            return;
        }

        $barberOptions = $session['barber_options'] ?? [];
        if (! isset($barberOptions[$input])) {
            $this->reply($phone, 'Opção inválida. Digite o número de um dos barbeiros acima (ou *0* para voltar).');

            return;
        }

        $session['barber_id'] = $barberOptions[$input];

        if ($isEditing && ($session['edit_mode'] ?? '') === 'barber' && ! empty($session['appointment_date'])) {
            $service = Service::find($session['service_id']);
            if ($service) {
                $slots = $this->calculateAvailableSlots(
                    $session['appointment_date'],
                    $session['barber_id'],
                    $service,
                    (int) $session['editing_appointment_id']
                );

                if (! empty($slots)) {
                    $this->promptChoosingTimeForDate($phone, $session, $cacheKey, $session['appointment_date']);

                    return;
                }
            }
        }

        $this->promptChoosingDate($phone, $session, $cacheKey);
    }

    protected function promptChoosingDate(string $phone, array $session, string $cacheKey, ?string $prefixNotice = null): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);
        $currentDate = $session['appointment_date'] ?? null;

        // Generate available dates (from today through end of current week, or next week if Sunday)
        $date = Carbon::now();
        if ($date->isSunday()) {
            $date->addDay();
        }
        $saturday = $date->copy()->endOfWeek()->subDay();

        $dateOptions = [];
        $text = $prefixNotice ? "{$prefixNotice}\n\n" : '';
        $text .= "📅 *Escolha a data do agendamento:*\n\n";
        $dIdx = 1;

        while ($date->lte($saturday)) {
            $formattedYmd = $date->format('Y-m-d');
            $dateOptions[$dIdx] = $formattedYmd;

            $dayLabel = $date->isToday()
                ? 'Hoje ('.$date->format('d/m').' - '.ucfirst($date->translatedFormat('l')).')'
                : ($date->isTomorrow()
                    ? 'Amanhã ('.$date->format('d/m').' - '.ucfirst($date->translatedFormat('l')).')'
                    : $date->format('d/m').' ('.ucfirst($date->translatedFormat('l')).')');

            $currentTag = ($isEditing && $currentDate === $formattedYmd) ? ' *(Data atual)*' : '';
            $text .= "{$dIdx} - {$dayLabel}{$currentTag}\n";
            $date->addDay();
            $dIdx++;
        }

        $text .= "\n0 - 🔙 Voltar\n\n";
        $text .= 'Envie o *número* da data:';

        $session['step'] = 'CHOOSING_DATE';
        $session['date_options'] = $dateOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply($phone, $text);
    }

    protected function handleChoosingDate(string $phone, string $input, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);

        if ($input === '0') {
            if ($isEditing) {
                if (($session['edit_mode'] ?? '') === 'barber') {
                    $this->promptChoosingBarber($phone, $session, $cacheKey);
                } else {
                    $this->returnToManageAppointment($phone, $session, $cacheKey);
                }
            } else {
                $this->promptChoosingBarber($phone, $session, $cacheKey);
            }

            return;
        }

        $dateOptions = $session['date_options'] ?? [];
        if (! isset($dateOptions[$input])) {
            $this->reply($phone, 'Data inválida. Envie o número de uma das datas listadas acima (ou *0* para voltar).');

            return;
        }

        $selectedDate = $dateOptions[$input];
        $session['appointment_date'] = $selectedDate;

        $this->promptChoosingTimeForDate($phone, $session, $cacheKey, $selectedDate);
    }

    protected function promptChoosingTimeForDate(string $phone, array $session, string $cacheKey, string $selectedDate): void
    {
        $service = Service::find($session['service_id'] ?? null);
        if (! $service) {
            $this->startScheduling($phone, $session, $cacheKey);

            return;
        }

        $isEditing = ! empty($session['editing_appointment_id']);
        $excludeId = $isEditing ? (int) $session['editing_appointment_id'] : null;

        // Calculate available slots
        $slots = $this->calculateAvailableSlots($selectedDate, $session['barber_id'], $service, $excludeId);

        if (empty($slots)) {
            $dateFormatted = Carbon::parse($selectedDate)->format('d/m/Y');
            if ($isEditing && ($session['edit_mode'] ?? '') === 'time') {
                $this->reply(
                    $phone,
                    "😕 Não há outros horários disponíveis para o dia *{$dateFormatted}* com este profissional.\n\n"
                    .'Envie *0* para voltar às opções de alteração (você pode tentar trocar a data ou o barbeiro).'
                );
                $session['step'] = 'CHOOSING_TIME';
                $session['time_options'] = [];
                Cache::put($cacheKey, $session, now()->addHours(2));

                return;
            }

            $this->promptChoosingDate(
                $phone,
                $session,
                $cacheKey,
                "😕 Puxa, não encontramos horários disponíveis para *{$dateFormatted}* com este profissional. Escolha outra data:"
            );

            return;
        }

        $timeOptions = [];
        $dateFormatted = Carbon::parse($selectedDate)->format('d/m/Y');
        $currentApptTime = $session['current_appt_time'] ?? null;
        $currentApptDate = $session['current_appt_date'] ?? null;

        $text = "⏰ *Horários disponíveis para {$dateFormatted}:*\n\n";

        $tIdx = 1;
        foreach ($slots as $slotInfo) {
            $timeOptions[$tIdx] = $slotInfo; // ['time' => '10:00', 'barber_id' => 1]
            $isCurrentSlot = ($isEditing && $currentApptDate === $selectedDate && $currentApptTime === $slotInfo['time']);
            $currentTag = $isCurrentSlot ? ' *(Horário atual)*' : '';
            $text .= "{$tIdx} - {$slotInfo['time']}{$currentTag}\n";
            $tIdx++;
        }

        $backText = ($isEditing && ($session['edit_mode'] ?? '') === 'time')
            ? 'Voltar às opções do agendamento'
            : 'Escolher outra data';

        $text .= "\n0 - 🔙 {$backText}\n\n";
        $text .= 'Envie o *número* do horário desejado:';

        $session['step'] = 'CHOOSING_TIME';
        $session['time_options'] = $timeOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply($phone, $text);
    }

    protected function handleChoosingTime(string $phone, string $input, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);

        if ($input === '0') {
            if ($isEditing && ($session['edit_mode'] ?? '') === 'time') {
                $this->returnToManageAppointment($phone, $session, $cacheKey);
            } else {
                $this->promptChoosingDate($phone, $session, $cacheKey);
            }

            return;
        }

        $timeOptions = $session['time_options'] ?? [];
        if (! isset($timeOptions[$input])) {
            $this->reply($phone, 'Horário inválido. Digite o número de um dos horários acima (ou *0* para voltar).');

            return;
        }

        $selectedSlot = $timeOptions[$input];
        $session['start_time'] = $selectedSlot['time'];
        $session['final_barber_id'] = $selectedSlot['barber_id'];

        if ($isEditing) {
            $appt = Appointment::with('customer')->find($session['editing_appointment_id']);
            if ($appt && $appt->customer) {
                $session['customer_name'] = $appt->customer->name;
                $session['customer_cpf'] = $appt->customer->cpf;
            }
            $session['step'] = 'CONFIRMING';
            Cache::put($cacheKey, $session, now()->addHours(2));
            $this->sendConfirmationSummary($phone, $session);

            return;
        }

        // Check if customer already exists in DB by WhatsApp number
        $cleanPhone = preg_replace('/\D/', '', $phone);
        $last9 = substr($cleanPhone, -9);
        $customer = Customer::where('whatsapp', 'like', "%{$last9}%")->first();

        if ($customer && ! empty($customer->name) && ! empty($customer->cpf)) {
            $session['customer_name'] = $customer->name;
            $session['customer_cpf'] = $customer->cpf;
            $session['step'] = 'CONFIRMING';
            Cache::put($cacheKey, $session, now()->addHours(2));
            $this->sendConfirmationSummary($phone, $session);
        } else {
            // Ask for customer's name
            $session['step'] = 'ASKING_NAME';
            Cache::put($cacheKey, $session, now()->addHours(2));

            $suggestedName = $session['pushName'] !== 'Cliente' ? $session['pushName'] : '';
            $msg = 'Quase pronto! 👤 Para finalizar seu agendamento, digite seu *Nome Completo*';
            if (! empty($suggestedName)) {
                $msg .= " (ou digite *1* para usar *{$suggestedName}*):";
            } else {
                $msg .= ':';
            }

            $this->reply($phone, $msg);
        }
    }

    protected function handleAskingName(string $phone, string $input, array $session, string $cacheKey): void
    {
        $name = trim($input);

        if ($name === '1' && ! empty($session['pushName']) && $session['pushName'] !== 'Cliente') {
            $name = $session['pushName'];
        }

        if (mb_strlen($name) < 3) {
            $this->reply($phone, 'Por favor, digite seu nome completo (mínimo 3 letras):');

            return;
        }

        $session['customer_name'] = $name;
        $session['step'] = 'ASKING_CPF';
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply(
            $phone,
            "Ótimo, *{$name}*! Agora, informe o seu *CPF* (apenas números ou formatado):"
        );
    }

    protected function handleAskingCpf(string $phone, string $input, array $session, string $cacheKey): void
    {
        $cpfClean = preg_replace('/\D/', '', $input);

        if (strlen($cpfClean) !== 11) {
            $this->reply($phone, 'CPF inválido. Por favor, envie os 11 dígitos do seu CPF (apenas números):');

            return;
        }

        // Format CPF: 000.000.000-00
        $formattedCpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfClean);
        $session['customer_cpf'] = $formattedCpf;
        $session['step'] = 'CONFIRMING';
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->sendConfirmationSummary($phone, $session);
    }

    protected function sendConfirmationSummary(string $phone, array $session): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);
        $service = Service::find($session['service_id']);
        $barber = User::find($session['final_barber_id']);
        $dateFormatted = Carbon::parse($session['appointment_date'])->format('d/m/Y');
        $price = $service ? number_format($service->price, 2, ',', '.') : '0,00';

        $header = $isEditing
            ? '📋 *Confirmação da Alteração de Agendamento:*'
            : '📋 *Confirmação do Agendamento:*';

        $confirmLabel = $isEditing
            ? '1️⃣ - ✅ *Confirmar Alteração*'
            : '1️⃣ - ✅ *Confirmar Agendamento*';

        $cancelLabel = $isEditing
            ? '0️⃣ - 🔙 *Descartar e Voltar*'
            : '0️⃣ - ❌ *Cancelar e Voltar ao Menu*';

        $summary = "{$header}\n\n"
                 ."👤 *Cliente:* {$session['customer_name']}\n"
                 ."✂️ *Serviço:* {$service->name} (R$ {$price})\n"
                 .'💈 *Barbeiro:* '.($barber ? $barber->name : 'Nossa Equipe')."\n"
                 ."📅 *Data:* {$dateFormatted}\n"
                 ."⏰ *Horário:* {$session['start_time']}\n\n"
                 ."Envie:\n"
                 ."{$confirmLabel}\n"
                 ."{$cancelLabel}";

        $this->reply($phone, $summary);
    }

    protected function handleConfirming(string $phone, string $input, array $session, string $cacheKey): void
    {
        $isEditing = ! empty($session['editing_appointment_id']);

        if ($input === '0') {
            if ($isEditing) {
                $this->returnToManageAppointment($phone, $session, $cacheKey);
            } else {
                Cache::forget($cacheKey);
                $this->reply($phone, 'Agendamento cancelado. Se precisar de algo mais, basta digitar *menu*!');
            }

            return;
        }

        if ($input !== '1') {
            $this->reply($phone, 'Digite *1* para confirmar ou *0* para cancelar.');

            return;
        }

        $service = Service::find($session['service_id']);
        $barber = User::find($session['final_barber_id']);

        if (! $service || ! $barber) {
            Cache::forget($cacheKey);
            $this->reply($phone, 'Ocorreu um erro ao localizar o serviço ou barbeiro. Por favor, reinicie digitando *menu*.');

            return;
        }

        $excludeId = $isEditing ? (int) $session['editing_appointment_id'] : null;

        // Re-verify slot availability to prevent double-booking race condition
        $availableSlots = $this->calculateAvailableSlots($session['appointment_date'], $barber->id, $service, $excludeId);
        $slotStillFree = collect($availableSlots)->contains(fn ($s) => $s['time'] === $session['start_time']);

        if (! $slotStillFree) {
            $this->reply(
                $phone,
                "⚠️ Puxa, o horário *{$session['start_time']}* acabou de ser preenchido! Por favor, escolha outro horário disponível:"
            );
            $this->promptChoosingTimeForDate($phone, $session, $cacheKey, $session['appointment_date']);

            return;
        }

        $endTime = Carbon::parse($session['appointment_date'].' '.$session['start_time'])
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

        if ($isEditing) {
            $this->confirmEditedAppointment($phone, $session, $cacheKey, $service, $barber, $endTime);

            return;
        }

        $cpfClean = preg_replace('/\D/', '', $session['customer_cpf']);

        // Create or update Customer
        $customer = Customer::firstOrCreate(
            ['cpf' => $cpfClean],
            ['name' => $session['customer_name'], 'whatsapp' => $phone]
        );

        if ($customer->name !== $session['customer_name'] || $customer->whatsapp !== $phone) {
            $customer->update([
                'name' => $session['customer_name'],
                'whatsapp' => $phone,
            ]);
        }

        $manageHash = Str::random(32);

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'user_id' => $barber->id,
            'service_id' => $service->id,
            'appointment_date' => $session['appointment_date'],
            'start_time' => $session['start_time'],
            'end_time' => $endTime,
            'status' => 'scheduled',
            'manage_hash' => $manageHash,
        ]);

        // Send Notification to Barber & Admins
        try {
            $barber->notify(new NewAppointmentNotification($appointment));

            if (! empty($barber->whatsapp) && $barber->whatsapp !== $phone) {
                $dateFormatted = Carbon::parse($session['appointment_date'])->format('d/m/Y');
                $barberMsg = "🔔 *Novo Agendamento via WhatsApp!*\n\n"
                           ."👤 Cliente: {$customer->name}\n"
                           ."✂️ Serviço: {$service->name}\n"
                           ."📅 Data: {$dateFormatted}\n"
                           ."⏰ Horário: {$session['start_time']}";
                SendWhatsAppMessage::dispatch($barber->whatsapp, $barberMsg);
            }

            $admins = User::whereJsonContains('roles', 'admin')
                ->where('id', '!=', $barber->id)
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewAppointmentNotification($appointment));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao notificar barbeiro: '.$e->getMessage());
        }

        // Clear Session
        Cache::forget($cacheKey);

        $dateFormatted = Carbon::parse($session['appointment_date'])->format('d/m/Y');
        $manageUrl = url('/agendamento/'.$manageHash);

        $finalMsg = "🎉 *AGENDAMENTO CONFIRMADO COM SUCESSO!* 🎉\n\n"
                  ."Muito obrigado, *{$customer->name}*! Seu horário já está reservado em nosso sistema.\n\n"
                  ."📅 *Data:* {$dateFormatted}\n"
                  ."⏰ *Horário:* {$session['start_time']}\n"
                  ."💈 *Barbeiro:* {$barber->name}\n"
                  ."✂️ *Serviço:* {$service->name}\n"
                  ."📍 *Local:* Barbearia Barcelona\n\n"
                  ."Caso precise alterar ou cancelar seu horário, você pode enviar *2* no menu principal a qualquer momento ou acessar:\n"
                  ."{$manageUrl}\n\n"
                  .'Te esperamos lá!';

        $this->reply($phone, $finalMsg);
    }

    protected function confirmEditedAppointment(
        string $phone,
        array $session,
        string $cacheKey,
        Service $service,
        User $barber,
        string $endTime
    ): void {
        $appointment = Appointment::with(['user', 'service', 'customer'])->find($session['editing_appointment_id']);

        if (! $appointment || $appointment->status !== 'scheduled') {
            Cache::forget($cacheKey);
            $this->reply($phone, 'Este agendamento não está mais disponível para alteração. Envie *menu* para iniciar.');

            return;
        }

        $oldBarber = $appointment->user;
        $oldDateFormatted = Carbon::parse($appointment->appointment_date)->format('d/m/Y');
        $oldTime = substr($appointment->start_time, 0, 5);
        $oldServiceName = $appointment->service ? $appointment->service->name : 'Serviço';
        $customerName = $appointment->customer ? $appointment->customer->name : ($session['customer_name'] ?? 'Cliente');

        $appointment->update([
            'user_id' => $barber->id,
            'service_id' => $service->id,
            'appointment_date' => $session['appointment_date'],
            'start_time' => $session['start_time'],
            'end_time' => $endTime,
        ]);

        $newDateFormatted = Carbon::parse($session['appointment_date'])->format('d/m/Y');

        // Notify Barber(s)
        try {
            if ($oldBarber && $oldBarber->id !== $barber->id) {
                if (! empty($oldBarber->whatsapp) && $oldBarber->whatsapp !== $phone) {
                    $msgOldBarber = "🔄 *Agendamento Transferido via WhatsApp*\n\n"
                                  ."O cliente *{$customerName}* transferiu o agendamento de *{$oldDateFormatted} às {$oldTime}* ({$oldServiceName}) para outro profissional.";
                    SendWhatsAppMessage::dispatch($oldBarber->whatsapp, $msgOldBarber);
                }

                $barber->notify(new NewAppointmentNotification($appointment));

                if (! empty($barber->whatsapp) && $barber->whatsapp !== $phone) {
                    $msgNewBarber = "🔔 *Agendamento Transferido para Você!*\n\n"
                                  ."👤 Cliente: {$customerName}\n"
                                  ."✂️ Serviço: {$service->name}\n"
                                  ."📅 Data: {$newDateFormatted}\n"
                                  ."⏰ Horário: {$session['start_time']}";
                    SendWhatsAppMessage::dispatch($barber->whatsapp, $msgNewBarber);
                }
            } elseif (! empty($barber->whatsapp) && $barber->whatsapp !== $phone) {
                $msgBarber = "🔄 *Agendamento Alterado via WhatsApp*\n\n"
                           ."👤 Cliente: {$customerName}\n"
                           ."❌ *Antes:* {$oldDateFormatted} às {$oldTime} ({$oldServiceName})\n"
                           ."✅ *Agora:* {$newDateFormatted} às {$session['start_time']} ({$service->name})";
                SendWhatsAppMessage::dispatch($barber->whatsapp, $msgBarber);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao notificar barbeiro sobre alteração: '.$e->getMessage());
        }

        Cache::forget($cacheKey);

        $manageUrl = url('/agendamento/'.$appointment->manage_hash);

        $finalMsg = "✅ *AGENDAMENTO ATUALIZADO COM SUCESSO!* ✅\n\n"
                  ."Tudo certo, *{$customerName}*! Seu agendamento foi remarcado com os novos dados:\n\n"
                  ."📅 *Nova Data:* {$newDateFormatted}\n"
                  ."⏰ *Novo Horário:* {$session['start_time']}\n"
                  ."💈 *Barbeiro:* {$barber->name}\n"
                  ."✂️ *Serviço:* {$service->name}\n"
                  ."📍 *Local:* Barbearia Barcelona\n\n"
                  ."Se precisar fazer outra alteração, basta enviar *menu* ou acessar:\n"
                  ."{$manageUrl}\n\n"
                  .'Te esperamos lá!';

        $this->reply($phone, $finalMsg);
    }

    /**
     * Show Customer Appointments (Option 2)
     */
    protected function showCustomerAppointments(string $phone, array $session, string $cacheKey): void
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);
        $last9 = substr($cleanPhone, -9);

        $customerIds = Customer::where('whatsapp', 'like', "%{$last9}%")->pluck('id');

        if ($customerIds->isEmpty()) {
            $this->reply(
                $phone,
                "Você ainda não possui nenhum agendamento cadastrado conosco.\n\nDigite *1* para agendar agora ou *menu* para voltar."
            );

            return;
        }

        $upcomingList = Appointment::with(['service', 'user', 'customer'])
            ->whereIn('customer_id', $customerIds)
            ->where('appointment_date', '>=', date('Y-m-d'))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        if ($upcomingList->isEmpty()) {
            $customer = Customer::find($customerIds->first());
            $customerName = $customer ? $customer->name : ($session['pushName'] ?? 'Cliente');
            $this->reply(
                $phone,
                "Olá, *{$customerName}*! Você não possui nenhum agendamento pendente no momento.\n\nDigite *1* para agendar um novo horário ou *menu* para voltar."
            );

            return;
        }

        if ($upcomingList->count() === 1) {
            $this->showManageAppointmentMenu($phone, $upcomingList->first(), $session, $cacheKey);

            return;
        }

        // Multiple upcoming appointments: let customer choose which one to manage
        $apptOptions = [];
        $text = "📋 *Seus Próximos Agendamentos:*\n\n";
        $idx = 1;

        foreach ($upcomingList as $appt) {
            $apptOptions[$idx] = $appt->id;
            $dateFormatted = Carbon::parse($appt->appointment_date)->format('d/m/Y');
            $time = substr($appt->start_time, 0, 5);
            $barberName = $appt->user ? $appt->user->name : 'Equipe';
            $serviceName = $appt->service ? $appt->service->name : 'Serviço';
            $text .= "{$idx}️⃣ - *{$dateFormatted} às {$time}* | {$serviceName} com *{$barberName}*\n";
            $idx++;
        }

        $text .= "\n0️⃣ - 🔙 *Voltar ao Menu Principal*\n\n";
        $text .= 'Envie o *número* do agendamento que deseja alterar ou cancelar:';

        $session['step'] = 'SELECTING_APPT';
        $session['appt_options'] = $apptOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->reply($phone, $text);
    }

    protected function handleSelectingAppt(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            $this->sendMainMenu($phone, $session['pushName'] ?? 'Cliente');

            return;
        }

        $apptOptions = $session['appt_options'] ?? [];
        if (! isset($apptOptions[$input])) {
            $this->reply($phone, 'Opção inválida. Envie o número de um dos agendamentos listados acima (ou *0* para voltar).');

            return;
        }

        $appt = Appointment::with(['service', 'user', 'customer'])->find($apptOptions[$input]);
        if (! $appt || $appt->status !== 'scheduled') {
            $this->showCustomerAppointments($phone, $session, $cacheKey);

            return;
        }

        $this->showManageAppointmentMenu($phone, $appt, $session, $cacheKey);
    }

    protected function returnToManageAppointment(string $phone, array $session, string $cacheKey): void
    {
        $apptId = $session['editing_appointment_id'] ?? null;
        if ($apptId) {
            $appt = Appointment::with(['service', 'user', 'customer'])->find($apptId);
            if ($appt && $appt->status === 'scheduled') {
                $this->showManageAppointmentMenu($phone, $appt, $session, $cacheKey);

                return;
            }
        }

        $this->showCustomerAppointments($phone, $session, $cacheKey);
    }

    protected function showManageAppointmentMenu(string $phone, Appointment $upcoming, array $session, string $cacheKey): void
    {
        $dateFormatted = Carbon::parse($upcoming->appointment_date)->format('d/m/Y');
        $time = substr($upcoming->start_time, 0, 5);
        $barberName = $upcoming->user ? $upcoming->user->name : 'Equipe';
        $serviceName = $upcoming->service ? $upcoming->service->name : 'Serviço';
        $price = $upcoming->service ? number_format($upcoming->service->price, 2, ',', '.') : '0,00';
        $customerName = $upcoming->customer ? $upcoming->customer->name : ($session['pushName'] ?? 'Cliente');

        $session['step'] = 'MANAGING_APPT';
        $session['editing_appointment_id'] = $upcoming->id;
        $session['cancel_appointment_id'] = $upcoming->id;
        $session['service_id'] = $upcoming->service_id;
        $session['barber_id'] = $upcoming->user_id;
        $session['final_barber_id'] = $upcoming->user_id;
        $session['appointment_date'] = $upcoming->appointment_date;
        $session['current_appt_date'] = $upcoming->appointment_date;
        $session['current_appt_time'] = $time;
        unset($session['edit_mode']);

        Cache::put($cacheKey, $session, now()->addHours(2));

        $msg = "📋 *Seu Agendamento Atual:*\n\n"
             ."👤 *Cliente:* {$customerName}\n"
             ."✂️ *Serviço:* {$serviceName} (R$ {$price})\n"
             ."💈 *Barbeiro:* {$barberName}\n"
             ."📅 *Data:* {$dateFormatted}\n"
             ."⏰ *Horário:* {$time}\n\n"
             ."O que você deseja fazer? Envie o *número* da opção:\n\n"
             ."1️⃣ - ⏰ *Trocar Horário* (mesma data e barbeiro)\n"
             ."2️⃣ - 📅 *Trocar Data* (e escolher horário)\n"
             ."3️⃣ - 💈 *Trocar Barbeiro*\n"
             ."4️⃣ - ✂️ *Trocar Serviço*\n"
             ."5️⃣ - ❌ *Cancelar Agendamento*\n"
             .'0️⃣ - 🔙 *Voltar ao Menu Principal*';

        $this->reply($phone, $msg);
    }

    protected function handleManagingAppt(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            $this->sendMainMenu($phone, $session['pushName'] ?? 'Cliente');

            return;
        }

        $apptId = $session['editing_appointment_id'] ?? null;
        $appt = Appointment::with(['service', 'user', 'customer'])->find($apptId);

        if (! $appt || $appt->status !== 'scheduled') {
            Cache::forget($cacheKey);
            $this->reply($phone, 'Este agendamento não foi encontrado ou já foi encerrado. Envie *menu* para voltar ao início.');

            return;
        }

        if ($input === '1') {
            // 1 - Trocar Horário (mantendo data, barbeiro e serviço)
            $session['edit_mode'] = 'time';
            $session['service_id'] = $appt->service_id;
            $session['barber_id'] = $appt->user_id;
            $session['appointment_date'] = $appt->appointment_date;
            $this->promptChoosingTimeForDate($phone, $session, $cacheKey, $appt->appointment_date);
        } elseif ($input === '2') {
            // 2 - Trocar Data (mantendo barbeiro e serviço, depois escolhe horário)
            $session['edit_mode'] = 'date';
            $session['service_id'] = $appt->service_id;
            $session['barber_id'] = $appt->user_id;
            $this->promptChoosingDate($phone, $session, $cacheKey);
        } elseif ($input === '3') {
            // 3 - Trocar Barbeiro
            $session['edit_mode'] = 'barber';
            $session['service_id'] = $appt->service_id;
            $session['appointment_date'] = $appt->appointment_date;
            $this->promptChoosingBarber($phone, $session, $cacheKey);
        } elseif ($input === '4') {
            // 4 - Trocar Serviço
            $session['edit_mode'] = 'service';
            $session['barber_id'] = $appt->user_id;
            $session['appointment_date'] = $appt->appointment_date;
            $this->startScheduling($phone, $session, $cacheKey);
        } elseif ($input === '5') {
            // 5 - Cancelar Agendamento
            $dateFormatted = Carbon::parse($appt->appointment_date)->format('d/m/Y');
            $time = substr($appt->start_time, 0, 5);
            $barberName = $appt->user ? $appt->user->name : 'Equipe';
            $serviceName = $appt->service ? $appt->service->name : 'Serviço';

            $session['step'] = 'CONFIRM_CANCEL_APPT';
            $session['cancel_appointment_id'] = $appt->id;
            Cache::put($cacheKey, $session, now()->addHours(2));

            $msg = "⚠️ *Confirmação de Cancelamento*\n\n"
                 ."Tem certeza que deseja cancelar o agendamento abaixo?\n\n"
                 ."✂️ *Serviço:* {$serviceName}\n"
                 ."💈 *Barbeiro:* {$barberName}\n"
                 ."📅 *Data:* {$dateFormatted} às {$time}\n\n"
                 ."1️⃣ - ❌ *Sim, cancelar este agendamento*\n"
                 .'0️⃣ - 🔙 *Não, voltar às opções*';

            $this->reply($phone, $msg);
        } else {
            $this->reply($phone, 'Opção inválida. Envie *1* (Horário), *2* (Data), *3* (Barbeiro), *4* (Serviço), *5* (Cancelar) ou *0* (Voltar).');
        }
    }

    protected function handleConfirmCancelAppt(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            $this->returnToManageAppointment($phone, $session, $cacheKey);

            return;
        }

        if ($input === '1') {
            $apptId = $session['cancel_appointment_id'] ?? null;
            $appt = Appointment::with(['user', 'service', 'customer'])->find($apptId);

            if ($appt && $appt->status === 'scheduled') {
                $appt->update(['status' => 'canceled']);

                // Notify barber
                if ($appt->user && ! empty($appt->user->whatsapp) && $appt->user->whatsapp !== $phone) {
                    $dateFormatted = Carbon::parse($appt->appointment_date)->format('d/m/Y');
                    $customerName = $appt->customer ? $appt->customer->name : 'Cliente';
                    $serviceName = $appt->service ? $appt->service->name : 'Serviço';
                    $msgBarber = "❌ *Agendamento Cancelado via WhatsApp*\n\n"
                               ."👤 Cliente: {$customerName}\n"
                               ."✂️ Serviço: {$serviceName}\n"
                               ."📅 Data: {$dateFormatted} às ".substr($appt->start_time, 0, 5);
                    SendWhatsAppMessage::dispatch($appt->user->whatsapp, $msgBarber);
                }

                Cache::forget($cacheKey);
                $this->reply($phone, "✅ Seu agendamento foi cancelado com sucesso.\n\nQuando quiser agendar novamente, basta digitar *menu*!");

                return;
            }
        }

        $this->reply($phone, 'Opção inválida. Digite *1* para confirmar o cancelamento ou *0* para voltar.');
    }

    /**
     * Show Services List (Option 3)
     */
    protected function showServicesList(string $phone): void
    {
        $services = Service::where('is_active', true)->orderBy('price', 'asc')->get();

        $text = "💈 *Tabela de Serviços - Barbearia Barcelona* 💈\n\n";

        foreach ($services as $srv) {
            $price = number_format($srv->price, 2, ',', '.');
            $text .= "✂️ *{$srv->name}* - R$ {$price}\n";
            $text .= "⏳ Duração: {$srv->duration_minutes} min\n";
            if (! empty($srv->description)) {
                $text .= "_{$srv->description}_\n";
            }
            $text .= "\n";
        }

        $text .= 'Digite *1* para agendar um horário ou *menu* para voltar.';

        $this->reply($phone, $text);
    }

    /**
     * Show About & Location (Option 4)
     */
    protected function showAboutAndLocation(string $phone): void
    {
        $text = "📍 *Barbearia Barcelona*\n\n"
              ."🏠 *Endereço:* Rua da Tradição, 123 - Centro, Barcelona - BA\n"
              ."⏰ *Funcionamento:* Segunda a Sábado, das 09:00 às 20:00\n"
              ."☕ Ambiente climatizado, chopp/café cortesia e música ambiente selecionada.\n\n"
              .'Digite *1* para agendar seu horário ou *menu* para voltar.';

        $this->reply($phone, $text);
    }

    /**
     * Slot calculation engine
     */
    protected function calculateAvailableSlots(string $date, $barberId, Service $service, ?int $excludeAppointmentId = null): array
    {
        $availableSlots = [];
        $duration = $service->duration_minutes;

        // Candidate barbers
        if ($barberId === 'any' || empty($barberId)) {
            $barbers = User::whereNotNull('roles')
                ->where('is_active', true)
                ->get()
                ->filter(fn ($u) => in_array('barber', $u->roles ?? []));
        } else {
            $barber = User::find($barberId);
            $barbers = ($barber && $barber->is_active) ? collect([$barber]) : collect();
        }

        if ($barbers->isEmpty()) {
            return [];
        }

        foreach ($barbers as $b) {
            $startWork = $b->work_start_time ?: '09:00:00';
            $endWork = $b->work_end_time ?: '19:00:00';

            $startOfDay = Carbon::parse($date.' '.$startWork);
            $endOfDay = Carbon::parse($date.' '.$endWork);

            $appointments = Appointment::where('user_id', $b->id)
                ->where('appointment_date', $date)
                ->when($excludeAppointmentId, fn ($q) => $q->where('id', '!=', $excludeAppointmentId))
                ->whereIn('status', ['scheduled', 'in_progress', 'awaiting_payment'])
                ->get();

            $currentSlot = $startOfDay->copy();

            while ($currentSlot->copy()->addMinutes($duration)->lte($endOfDay)) {
                $slotStart = $currentSlot->copy();
                $slotEnd = $currentSlot->copy()->addMinutes($duration);

                $isAvailable = true;

                foreach ($appointments as $appt) {
                    $apptStart = Carbon::parse($date.' '.$appt->start_time);
                    $apptEnd = Carbon::parse($date.' '.$appt->end_time);

                    if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($date === date('Y-m-d') && $slotStart->lt(now())) {
                    $isAvailable = false;
                }

                if ($isAvailable) {
                    $timeStr = $slotStart->format('H:i');
                    // Ensure unique by time
                    if (! isset($availableSlots[$timeStr])) {
                        $availableSlots[$timeStr] = [
                            'time' => $timeStr,
                            'barber_id' => $b->id,
                        ];
                    }
                }

                $currentSlot->addMinutes(15);
            }
        }

        ksort($availableSlots);

        return array_values($availableSlots);
    }

    protected function getGreeting(): string
    {
        $hour = (int) date('H');
        if ($hour >= 5 && $hour < 12) {
            return 'Bom dia';
        } elseif ($hour >= 12 && $hour < 18) {
            return 'Boa tarde';
        }

        return 'Boa noite';
    }
}
