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
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Process an incoming message from WhatsApp Webhook.
     */
    public function processMessage(string $phone, string $text, string $pushName = 'Cliente'): void
    {
        $cleanText = trim($text);
        $lowerText = mb_strtolower($cleanText);

        // Normalize phone for caching
        $phoneKey = preg_replace('/\D/', '', $phone);
        $cacheKey = "whatsapp_bot_session_{$phoneKey}";

        // Global command to reset to main menu
        if (in_array($lowerText, ['menu', 'inicio', 'início', 'começar', 'ola', 'olá', 'oi', 'ajuda', 'cancelar', 'sair', 'voltar', 'reset'])) {
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
             ."2️⃣ *Meus Agendamentos (Ver ou Cancelar)*\n"
             ."3️⃣ *Ver Serviços e Valores*\n"
             ."4️⃣ *Endereço e Contato*\n\n"
             .'_(Dica: você pode digitar *menu* a qualquer momento para voltar aqui)_';

        $this->whatsAppService->sendMessage($phone, $msg);
    }

    protected function handleMenu(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '1') {
            $this->startScheduling($phone, $session, $cacheKey);
        } elseif ($input === '2') {
            $this->showCustomerAppointments($phone, $session, $cacheKey);
        } elseif ($input === '3') {
            $this->showServicesList($phone);
        } elseif ($input === '4') {
            $this->showAboutAndLocation($phone);
        } else {
            $this->whatsAppService->sendMessage(
                $phone,
                'Opção inválida 🤔 Por favor, envie *1*, *2*, *3* ou *4* de acordo com o que você deseja:'
            );
        }
    }

    /**
     * Step 1: Start Scheduling - List Services
     */
    protected function startScheduling(string $phone, array $session, string $cacheKey): void
    {
        $services = Service::where('is_active', true)->orderBy('price', 'asc')->get();

        if ($services->isEmpty()) {
            $this->whatsAppService->sendMessage($phone, 'No momento não há serviços disponíveis para agendamento. Por favor, tente novamente mais tarde.');

            return;
        }

        $options = [];
        $text = "✂️ *Escolha o serviço desejado:*\n\n";

        $num = 1;
        foreach ($services as $srv) {
            $options[$num] = $srv->id;
            $formattedPrice = number_format($srv->price, 2, ',', '.');
            $text .= "{$num} - *{$srv->name}* (R$ {$formattedPrice} - {$srv->duration_minutes} min)\n";
            $num++;
        }

        $text .= "\n0 - 🔙 Voltar ao menu principal\n\n";
        $text .= 'Envie o *número* do serviço:';

        $session['step'] = 'CHOOSING_SERVICE';
        $session['service_options'] = $options;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->whatsAppService->sendMessage($phone, $text);
    }

    protected function handleChoosingService(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            $this->sendMainMenu($phone, $session['pushName'] ?? 'Cliente');

            return;
        }

        $serviceOptions = $session['service_options'] ?? [];
        if (! isset($serviceOptions[$input])) {
            $this->whatsAppService->sendMessage($phone, 'Número inválido. Por favor, digite o número correspondente a um dos serviços listados acima (ou *0* para voltar).');

            return;
        }

        $serviceId = $serviceOptions[$input];
        $service = Service::find($serviceId);

        if (! $service) {
            $this->startScheduling($phone, $session, $cacheKey);

            return;
        }

        $session['service_id'] = $serviceId;

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
            $text .= "{$idx} - {$barber->name}\n";
            $idx++;
        }

        $text .= "\n0 - 🔙 Voltar\n\n";
        $text .= 'Envie o *número* do profissional:';

        $session['step'] = 'CHOOSING_BARBER';
        $session['barber_options'] = $barberOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->whatsAppService->sendMessage($phone, $text);
    }

    protected function handleChoosingBarber(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            $this->startScheduling($phone, $session, $cacheKey);

            return;
        }

        $barberOptions = $session['barber_options'] ?? [];
        if (! isset($barberOptions[$input])) {
            $this->whatsAppService->sendMessage($phone, 'Opção inválida. Digite o número de um dos barbeiros acima (ou *0* para voltar).');

            return;
        }

        $session['barber_id'] = $barberOptions[$input];

        // Generate available dates (from today through end of current week, or next week if Sunday)
        $date = Carbon::now();
        if ($date->isSunday()) {
            $date->addDay();
        }
        $saturday = $date->copy()->endOfWeek()->subDay();

        $dateOptions = [];
        $text = "📅 *Escolha a data do agendamento:*\n\n";
        $dIdx = 1;

        while ($date->lte($saturday)) {
            $formattedYmd = $date->format('Y-m-d');
            $dateOptions[$dIdx] = $formattedYmd;

            $dayLabel = $date->isToday()
                ? 'Hoje ('.$date->format('d/m').' - '.ucfirst($date->translatedFormat('l')).')'
                : ($date->isTomorrow()
                    ? 'Amanhã ('.$date->format('d/m').' - '.ucfirst($date->translatedFormat('l')).')'
                    : $date->format('d/m').' ('.ucfirst($date->translatedFormat('l')).')');

            $text .= "{$dIdx} - {$dayLabel}\n";
            $date->addDay();
            $dIdx++;
        }

        $text .= "\n0 - 🔙 Voltar\n\n";
        $text .= 'Envie o *número* da data:';

        $session['step'] = 'CHOOSING_DATE';
        $session['date_options'] = $dateOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->whatsAppService->sendMessage($phone, $text);
    }

    protected function handleChoosingDate(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            // Re-trigger barber selection
            $session['step'] = 'CHOOSING_SERVICE';
            $this->handleChoosingService($phone, (string) array_search($session['service_id'], $session['service_options'] ?? []), $session, $cacheKey);

            return;
        }

        $dateOptions = $session['date_options'] ?? [];
        if (! isset($dateOptions[$input])) {
            $this->whatsAppService->sendMessage($phone, 'Data inválida. Envie o número de uma das datas listadas acima (ou *0* para voltar).');

            return;
        }

        $selectedDate = $dateOptions[$input];
        $session['appointment_date'] = $selectedDate;

        $service = Service::find($session['service_id']);
        if (! $service) {
            $this->startScheduling($phone, $session, $cacheKey);

            return;
        }

        // Calculate available slots
        $slots = $this->calculateAvailableSlots($selectedDate, $session['barber_id'], $service);

        if (empty($slots)) {
            $this->whatsAppService->sendMessage(
                $phone,
                "😕 Puxa, não encontramos horários disponíveis para este dia com este profissional.\n\n"
                .'Digite *0* para escolher outra data ou profissional, ou envie *menu* para reiniciar.'
            );

            return;
        }

        $timeOptions = [];
        $dateFormatted = Carbon::parse($selectedDate)->format('d/m/Y');
        $text = "⏰ *Horários disponíveis para {$dateFormatted}:*\n\n";

        $tIdx = 1;
        foreach ($slots as $slotInfo) {
            $timeOptions[$tIdx] = $slotInfo; // ['time' => '10:00', 'barber_id' => 1]
            $text .= "{$tIdx} - {$slotInfo['time']}\n";
            $tIdx++;
        }

        $text .= "\n0 - 🔙 Escolher outra data\n\n";
        $text .= 'Envie o *número* do horário desejado:';

        $session['step'] = 'CHOOSING_TIME';
        $session['time_options'] = $timeOptions;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->whatsAppService->sendMessage($phone, $text);
    }

    protected function handleChoosingTime(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            // Re-trigger date selection
            $session['step'] = 'CHOOSING_BARBER';
            $this->handleChoosingBarber($phone, (string) array_search($session['barber_id'], $session['barber_options'] ?? []), $session, $cacheKey);

            return;
        }

        $timeOptions = $session['time_options'] ?? [];
        if (! isset($timeOptions[$input])) {
            $this->whatsAppService->sendMessage($phone, 'Horário inválido. Digite o número de um dos horários acima (ou *0* para voltar).');

            return;
        }

        $selectedSlot = $timeOptions[$input];
        $session['start_time'] = $selectedSlot['time'];
        $session['final_barber_id'] = $selectedSlot['barber_id'];

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

            $this->whatsAppService->sendMessage($phone, $msg);
        }
    }

    protected function handleAskingName(string $phone, string $input, array $session, string $cacheKey): void
    {
        $name = trim($input);

        if ($name === '1' && ! empty($session['pushName']) && $session['pushName'] !== 'Cliente') {
            $name = $session['pushName'];
        }

        if (mb_strlen($name) < 3) {
            $this->whatsAppService->sendMessage($phone, 'Por favor, digite seu nome completo (mínimo 3 letras):');

            return;
        }

        $session['customer_name'] = $name;
        $session['step'] = 'ASKING_CPF';
        Cache::put($cacheKey, $session, now()->addHours(2));

        $this->whatsAppService->sendMessage(
            $phone,
            "Ótimo, *{$name}*! Agora, informe o seu *CPF* (apenas números ou formatado):"
        );
    }

    protected function handleAskingCpf(string $phone, string $input, array $session, string $cacheKey): void
    {
        $cpfClean = preg_replace('/\D/', '', $input);

        if (strlen($cpfClean) !== 11) {
            $this->whatsAppService->sendMessage($phone, 'CPF inválido. Por favor, envie os 11 dígitos do seu CPF (apenas números):');

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
        $service = Service::find($session['service_id']);
        $barber = User::find($session['final_barber_id']);
        $dateFormatted = Carbon::parse($session['appointment_date'])->format('d/m/Y');
        $price = $service ? number_format($service->price, 2, ',', '.') : '0,00';

        $summary = "📋 *Confirmação do Agendamento:*\n\n"
                 ."👤 *Cliente:* {$session['customer_name']}\n"
                 ."📄 *CPF:* {$session['customer_cpf']}\n"
                 ."✂️ *Serviço:* {$service->name} (R$ {$price})\n"
                 .'💈 *Barbeiro:* '.($barber ? $barber->name : 'Nossa Equipe')."\n"
                 ."📅 *Data:* {$dateFormatted}\n"
                 ."⏰ *Horário:* {$session['start_time']}\n\n"
                 ."Envie:\n"
                 ."1️⃣ - ✅ *Confirmar Agendamento*\n"
                 .'0️⃣ - ❌ *Cancelar e Voltar ao Menu*';

        $this->whatsAppService->sendMessage($phone, $summary);
    }

    protected function handleConfirming(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '0') {
            Cache::forget($cacheKey);
            $this->whatsAppService->sendMessage($phone, 'Agendamento cancelado. Se precisar de algo mais, basta digitar *menu*!');

            return;
        }

        if ($input !== '1') {
            $this->whatsAppService->sendMessage($phone, 'Digite *1* para confirmar o agendamento ou *0* para cancelar.');

            return;
        }

        $service = Service::find($session['service_id']);
        $barber = User::find($session['final_barber_id']);

        if (! $service || ! $barber) {
            Cache::forget($cacheKey);
            $this->whatsAppService->sendMessage($phone, 'Ocorreu um erro ao localizar o serviço ou barbeiro. Por favor, reinicie digitando *menu*.');

            return;
        }

        $endTime = Carbon::parse($session['appointment_date'].' '.$session['start_time'])
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

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

        // Send Notification to Barber
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
                  ."Caso precise alterar ou cancelar seu horário, você pode enviar *2* aqui a qualquer momento ou acessar:\n"
                  ."{$manageUrl}\n\n"
                  .'Te esperamos lá!';

        $this->whatsAppService->sendMessage($phone, $finalMsg);
    }

    /**
     * Show Customer Appointments (Option 2)
     */
    protected function showCustomerAppointments(string $phone, array $session, string $cacheKey): void
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);
        $last9 = substr($cleanPhone, -9);

        $customer = Customer::where('whatsapp', 'like', "%{$last9}%")->first();

        if (! $customer) {
            $this->whatsAppService->sendMessage(
                $phone,
                "Você ainda não possui nenhum agendamento cadastrado conosco.\n\nDigite *1* para agendar agora ou *menu* para voltar."
            );

            return;
        }

        $upcoming = Appointment::with(['service', 'user'])
            ->where('customer_id', $customer->id)
            ->where('appointment_date', '>=', date('Y-m-d'))
            ->where('status', 'scheduled')
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        if (! $upcoming) {
            $this->whatsAppService->sendMessage(
                $phone,
                "Olá, *{$customer->name}*! Você não possui nenhum agendamento pendente no momento.\n\nDigite *1* para agendar um novo horário ou *menu* para voltar."
            );

            return;
        }

        $dateFormatted = Carbon::parse($upcoming->appointment_date)->format('d/m/Y');
        $time = substr($upcoming->start_time, 0, 5);
        $barberName = $upcoming->user ? $upcoming->user->name : 'Equipe';
        $serviceName = $upcoming->service ? $upcoming->service->name : 'Serviço';

        $session['step'] = 'CONFIRM_CANCEL_APPT';
        $session['cancel_appointment_id'] = $upcoming->id;
        Cache::put($cacheKey, $session, now()->addHours(2));

        $msg = "📋 *Seu Agendamento Atual:*\n\n"
             ."👤 *Cliente:* {$customer->name}\n"
             ."✂️ *Serviço:* {$serviceName}\n"
             ."💈 *Barbeiro:* {$barberName}\n"
             ."📅 *Data:* {$dateFormatted} às {$time}\n\n"
             ."Deseja cancelar este horário?\n"
             ."1️⃣ - ❌ *Sim, cancelar este agendamento*\n"
             .'0️⃣ - 🔙 *Não, manter e voltar ao menu*';

        $this->whatsAppService->sendMessage($phone, $msg);
    }

    protected function handleConfirmCancelAppt(string $phone, string $input, array $session, string $cacheKey): void
    {
        if ($input === '1') {
            $apptId = $session['cancel_appointment_id'] ?? null;
            $appt = Appointment::find($apptId);

            if ($appt && $appt->status === 'scheduled') {
                $appt->update(['status' => 'canceled']);

                // Notify barber
                if ($appt->user && ! empty($appt->user->whatsapp)) {
                    $dateFormatted = Carbon::parse($appt->appointment_date)->format('d/m/Y');
                    $msgBarber = "⚠️ *Agendamento Cancelado via WhatsApp*\n\n"
                               ."O cliente cancelou o horário do dia {$dateFormatted} às ".substr($appt->start_time, 0, 5);
                    SendWhatsAppMessage::dispatch($appt->user->whatsapp, $msgBarber);
                }

                Cache::forget($cacheKey);
                $this->whatsAppService->sendMessage($phone, "✅ Seu agendamento foi cancelado com sucesso.\n\nQuando quiser remarcar, basta digitar *menu*!");

                return;
            }
        }

        Cache::forget($cacheKey);
        $this->whatsAppService->sendMessage($phone, 'Seu horário foi mantido! Se precisar de algo, envie *menu*.');
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

        $this->whatsAppService->sendMessage($phone, $text);
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

        $this->whatsAppService->sendMessage($phone, $text);
    }

    /**
     * Slot calculation engine
     */
    protected function calculateAvailableSlots(string $date, $barberId, Service $service): array
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
            $barbers = $barber ? collect([$barber]) : collect();
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
