<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class ManageAppointment extends Component
{
    public $appointment;
    public $hash;

    public $barber_id;
    public $service_id;
    public $start_time;

    public $available_slots = [];
    public $step = 1;
    public $message = '';

    public function mount($hash)
    {
        $this->hash = $hash;
        $this->appointment = Appointment::with(['user', 'service', 'customer'])
            ->where('manage_hash', $hash)
            ->firstOrFail();
        
        if ($this->appointment->status === 'canceled') {
            $this->step = 'canceled';
            $this->message = 'Este agendamento já foi cancelado.';
        } elseif (Carbon::parse($this->appointment->appointment_date)->lt(Carbon::today())) {
            $this->step = 'past';
            $this->message = 'Este agendamento é de uma data passada e não pode ser alterado.';
        } else {
            $this->barber_id = $this->appointment->user_id;
            $this->service_id = $this->appointment->service_id;
            $this->start_time = substr($this->appointment->start_time, 0, 5); // get HH:MM
            
            $this->generateSlots();
        }
    }

    public function updatedBarberId()
    {
        $this->generateSlots();
        $this->start_time = null;
    }

    public function updatedServiceId()
    {
        $this->generateSlots();
        $this->start_time = null;
    }

    public function selectTime($time)
    {
        $this->start_time = $time;
    }

    public function generateSlots()
    {
        $this->available_slots = [];
        
        if (!$this->appointment->appointment_date || !$this->barber_id || !$this->service_id) {
            return;
        }

        $barber = User::find($this->barber_id);
        $service = Service::find($this->service_id);
        
        if (!$barber || !$service) return;

        $duration = $service->duration_minutes;
        
        $appointments = Appointment::where('user_id', $this->barber_id)
            ->where('appointment_date', $this->appointment->appointment_date)
            ->where('id', '!=', $this->appointment->id)
            ->whereIn('status', ['scheduled', 'in_progress', 'awaiting_payment'])
            ->get();

        $startOfDay = Carbon::parse($this->appointment->appointment_date . ' ' . $barber->work_start_time);
        $endOfDay = Carbon::parse($this->appointment->appointment_date . ' ' . $barber->work_end_time);
        
        $currentSlot = $startOfDay->copy();

        while ($currentSlot->copy()->addMinutes($duration)->lte($endOfDay)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duration);
            
            $isAvailable = true;

            foreach ($appointments as $appt) {
                $apptStart = Carbon::parse($this->appointment->appointment_date . ' ' . $appt->start_time);
                $apptEnd = Carbon::parse($this->appointment->appointment_date . ' ' . $appt->end_time);

                if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($this->appointment->appointment_date === date('Y-m-d') && $slotStart->lt(now())) {
                $isAvailable = false;
            }

            if ($isAvailable) {
                $this->available_slots[] = $slotStart->format('H:i');
            }

            $currentSlot->addMinutes(15);
        }
    }

    public function updateAppointment()
    {
        $this->validate([
            'barber_id' => 'required',
            'service_id' => 'required',
            'start_time' => 'required',
        ]);

        $service = Service::find($this->service_id);
        $end_time = Carbon::parse($this->appointment->appointment_date . ' ' . $this->start_time)
            ->addMinutes($service->duration_minutes)
            ->format('H:i:s');

        $old_barber_id = $this->appointment->user_id;
        $old_time = substr($this->appointment->start_time, 0, 5);
        $old_service_name = $this->appointment->service->name;

        $this->appointment->update([
            'user_id' => $this->barber_id,
            'service_id' => $this->service_id,
            'start_time' => $this->start_time,
            'end_time' => $end_time,
        ]);

        $barber = User::find($this->barber_id);
        $dateFormatted = Carbon::parse($this->appointment->appointment_date)->format('d/m/Y');

        // Send WhatsApp to Customer about the update
        if (!empty($this->appointment->customer->whatsapp)) {
            $manageUrl = url('/agendamento/' . $this->hash);
            $msg = "Seu agendamento foi atualizado!\n\n📅 Data: {$dateFormatted}\n⏰ Horário: {$this->start_time}\n💈 Barbeiro: " . ($barber ? $barber->name : 'Nossa equipe') . "\n✂️ Serviço: {$service->name}\n\nPrecisa alterar ou cancelar? Acesse o link abaixo:\n{$manageUrl}";
            \App\Jobs\SendWhatsAppMessage::dispatch($this->appointment->customer->whatsapp, $msg);
        }

        // Send WhatsApp to Barber
        if ($barber && !empty($barber->whatsapp)) {
            $msgBarber = "⚠️ O cliente {$this->appointment->customer->name} alterou o agendamento do dia {$dateFormatted}.\n\nNovo Serviço: {$service->name}\nNovo Horário: {$this->start_time}";
            \App\Jobs\SendWhatsAppMessage::dispatch($barber->whatsapp, $msgBarber);
        }

        $this->step = 'success';
        $this->message = 'Agendamento atualizado com sucesso!';
    }

    public function cancelAppointment()
    {
        $this->appointment->update(['status' => 'canceled']);
        $this->step = 'canceled';
        $this->message = 'Agendamento cancelado com sucesso.';
        
        // Send WhatsApp to Customer about cancellation
        if (!empty($this->appointment->customer->whatsapp)) {
            $msg = "Seu agendamento foi cancelado com sucesso.\nQualquer dúvida, estamos à disposição!";
            \App\Jobs\SendWhatsAppMessage::dispatch($this->appointment->customer->whatsapp, $msg);
        }

        // Send WhatsApp to Barber
        if ($this->appointment->user && !empty($this->appointment->user->whatsapp)) {
            $dateFormatted = Carbon::parse($this->appointment->appointment_date)->format('d/m/Y');
            $timeFormatted = substr($this->appointment->start_time, 0, 5);
            $msgBarber = "❌ Cancelamento!\nO cliente {$this->appointment->customer->name} cancelou o agendamento.\n\nData: {$dateFormatted}\nHorário: {$timeFormatted}\nServiço: {$this->appointment->service->name}";
            \App\Jobs\SendWhatsAppMessage::dispatch($this->appointment->user->whatsapp, $msgBarber);
        }
    }

    public function render()
    {
        $barbers = User::whereNotNull('roles')
            ->where('is_active', true)
            ->get()
            ->filter(fn($u) => in_array('barber', $u->roles));
            
        $services = Service::where('is_active', true)->get();

        return view('livewire.manage-appointment', [
            'barbers' => $barbers,
            'services' => $services,
            'dateFormatted' => $this->appointment ? Carbon::parse($this->appointment->appointment_date)->format('d/m/Y') : ''
        ])->layout('components.layouts.guest');
    }
}
