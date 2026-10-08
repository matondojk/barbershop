<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class BarberAgenda extends Component
{
    public function mount()
    {
        abort_if(!in_array('barber', auth()->user()->roles ?? []), 403, 'Acesso restrito para barbeiros.');
    }

    public function updateStatus($id, $status)
    {
        $appointment = Appointment::where('user_id', auth()->id())->findOrFail($id);
        
        $validStatuses = ['scheduled', 'in_progress', 'awaiting_payment', 'rejected', 'canceled'];
        if (in_array($status, $validStatuses)) {
            $appointment->update(['status' => $status]);
        }
    }

    public function render()
    {
        $appointments = Appointment::with(['customer', 'service'])
            ->where('user_id', auth()->id())
            ->where('appointment_date', Carbon::today()->format('Y-m-d'))
            ->orderBy('start_time', 'asc')
            ->get();
            
        $stats = [
            'total_hoje' => $appointments->count(),
            'concluidos' => $appointments->whereIn('status', ['awaiting_payment', 'completed'])->count(),
            'aguardando' => $appointments->where('status', 'scheduled')->count() + $appointments->where('status', 'in_progress')->count(),
            'faturamento' => $appointments->whereIn('status', ['awaiting_payment', 'completed'])->sum(function($appt) {
                // If it's already completed and commission is registered
                if ($appt->commission_amount > 0) return $appt->commission_amount;
                
                // If awaiting payment, calculate estimated commission
                $val = $appt->service->price ?? 0;
                $user = auth()->user();
                if ($user->commission_type === 'percentage') {
                    return $val * ($user->commission_value / 100);
                } else {
                    return $user->commission_value;
                }
            }),
        ];

        $visibleAppointments = $appointments->reject(function($appt) {
            return $appt->status === 'completed';
        });

        return view('livewire.barber-agenda', [
            'appointments' => $visibleAppointments,
            'stats' => $stats
        ])->layout('components.layouts.app');
    }
}
