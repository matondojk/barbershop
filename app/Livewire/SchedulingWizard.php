<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class SchedulingWizard extends Component
{
    public $step = 1;
    
    // Form data
    public $customer_name;
    public $customer_cpf;
    public $customer_whatsapp;
    public $barber_id;
    public $service_id;
    public $appointment_date;
    public $start_time;

    // Derived data
    public $available_slots = [];
    public $existing_appointment = null;
    public $available_dates = [];

    protected $rules = [
        'customer_name' => 'required|min:3',
        'customer_cpf' => 'required|min:14', // 000.000.000-00
        'customer_whatsapp' => 'required|min:14', // (11) 99999-9999
    ];

    public function mount()
    {
        $date = Carbon::now();
        
        // Se hoje for domingo, joga para segunda-feira para mostrar a próxima semana
        if ($date->isSunday()) {
            $date->addDay(); 
        }

        // Fim da semana (Domingo)
        $endOfWeek = $date->copy()->endOfWeek(); 
        // Sábado dessa mesma semana
        $saturday = $endOfWeek->copy()->subDay(); 

        while ($date->lte($saturday)) {
            $this->available_dates[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->translatedFormat('l'),
                'day_number' => $date->format('d'),
                'month_short' => $date->translatedFormat('M')
            ];
            $date->addDay();
        }
    }

    public function nextStep1()
    {
        $this->validateOnly('customer_name');
        $this->validateOnly('customer_cpf');
        $this->validateOnly('customer_whatsapp');
        $this->step = 2;
    }

    public function selectBarber($id)
    {
        $this->barber_id = $id;
        $this->step = 3;
    }

    public function selectService($id)
    {
        $this->service_id = $id;
        $this->step = 4;
        $this->appointment_date = null;
        $this->start_time = null;
    }

    public function selectDate($date)
    {
        $this->appointment_date = $date;
        $this->start_time = null;
        
        $customer = Customer::where('cpf', preg_replace('/[^0-9]/', '', $this->customer_cpf))->first();

        if ($customer) {
            $this->existing_appointment = Appointment::where('customer_id', $customer->id)
                ->where('appointment_date', $this->appointment_date)
                ->where('status', 'scheduled')
                ->first();
        } else {
            $this->existing_appointment = null;
        }

        if (!$this->existing_appointment) {
            $this->generateSlots();
            $this->step = 5;
        } else {
            $this->step = 5;
        }
    }

    public function rescheduleExisting()
    {
        if ($this->existing_appointment) {
            $this->existing_appointment->update(['status' => 'canceled']);
            $this->existing_appointment = null;
            $this->generateSlots();
        }
    }

    public function selectTime($time)
    {
        $this->start_time = $time;
    }

    public function generateSlots()
    {
        $this->available_slots = [];
        
        if (!$this->appointment_date || !$this->barber_id || !$this->service_id) {
            return;
        }

        $barber = User::find($this->barber_id);
        $service = Service::find($this->service_id);
        
        if (!$barber || !$service) return;

        $duration = $service->duration_minutes;
        
        $appointments = Appointment::where('user_id', $this->barber_id)
            ->where('appointment_date', $this->appointment_date)
            ->whereIn('status', ['scheduled', 'in_progress', 'awaiting_payment'])
            ->get();

        $startOfDay = Carbon::parse($this->appointment_date . ' ' . $barber->work_start_time);
        $endOfDay = Carbon::parse($this->appointment_date . ' ' . $barber->work_end_time);
        
        $currentSlot = $startOfDay->copy();

        while ($currentSlot->copy()->addMinutes($duration)->lte($endOfDay)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duration);
            
            $isAvailable = true;

            foreach ($appointments as $appt) {
                $apptStart = Carbon::parse($this->appointment_date . ' ' . $appt->start_time);
                $apptEnd = Carbon::parse($this->appointment_date . ' ' . $appt->end_time);

                if ($slotStart->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($this->appointment_date === date('Y-m-d') && $slotStart->lt(now())) {
                $isAvailable = false;
            }

            if ($isAvailable) {
                $this->available_slots[] = $slotStart->format('H:i');
            }

            $currentSlot->addMinutes(15);
        }
    }

    public function confirm()
    {
        $this->validate([
            'customer_name' => 'required',
            'customer_cpf' => 'required',
            'customer_whatsapp' => 'required',
            'barber_id' => 'required',
            'service_id' => 'required',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
        ]);

        $service = Service::find($this->service_id);
        
        $end_time = Carbon::parse($this->appointment_date . ' ' . $this->start_time)
            ->addMinutes($service->duration_minutes)
            ->format('H:i');

        $cpfLimpo = preg_replace('/[^0-9]/', '', $this->customer_cpf);

        // Create or update Customer based on CPF
        $customer = Customer::firstOrCreate(
            ['cpf' => $cpfLimpo],
            ['name' => $this->customer_name, 'whatsapp' => $this->customer_whatsapp]
        );
        // If name or whatsapp changed, update it
        if ($customer->name !== $this->customer_name || $customer->whatsapp !== $this->customer_whatsapp) {
            $customer->update([
                'name' => $this->customer_name,
                'whatsapp' => $this->customer_whatsapp
            ]);
        }

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'user_id' => $this->barber_id,
            'service_id' => $this->service_id,
            'appointment_date' => $this->appointment_date,
            'start_time' => $this->start_time,
            'end_time' => $end_time,
            'status' => 'scheduled',
        ]);

        // Send Notification to Barber
        $barber = User::find($this->barber_id);
        if ($barber) {
            $barber->notify(new \App\Notifications\NewAppointmentNotification($appointment));
        }

        // Send Notification to Admins (excluding barber if barber is admin to avoid duplicates)
        $admins = User::whereJsonContains('roles', 'admin')
            ->where('id', '!=', $this->barber_id)
            ->get();
            
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewAppointmentNotification($appointment));
        }

        $this->step = 6;
    }

    public function render()
    {
        $barbers = User::whereNotNull('roles')
            ->where('is_active', true)
            ->get()
            ->filter(fn($u) => in_array('barber', $u->roles));
            
        $services = Service::where('is_active', true)->get();

        return view('livewire.scheduling-wizard', [
            'barbers' => $barbers,
            'services' => $services,
        ]);
    }
}
