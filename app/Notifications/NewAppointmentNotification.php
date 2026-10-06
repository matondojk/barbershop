<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    public $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'customer_name' => $this->appointment->customer->name,
            'appointment_date' => $this->appointment->appointment_date,
            'start_time' => $this->appointment->start_time,
            'service_name' => $this->appointment->service->name,
            'barber_name' => $this->appointment->user->name,
        ];
    }
}
