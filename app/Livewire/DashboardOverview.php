<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class DashboardOverview extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        $query = Appointment::with(['customer', 'user', 'service'])
            ->where('appointment_date', Carbon::today()->format('Y-m-d'))
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('start_time', 'asc');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $appointments = $query->get();

        return view('livewire.dashboard-overview', [
            'appointments' => $appointments
        ])->layout('components.layouts.app');
    }
}
