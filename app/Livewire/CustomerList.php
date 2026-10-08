<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Appointment;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_name = '';
    public $filter_cpf = '';
    public $filter_whatsapp = '';
    
    public $showHistoryModal = false;
    public $selectedCustomer = null;
    public $customerHistory = [];

    public function mount()
    {
        abort_if(!auth()->check(), 403, 'Acesso não autorizado.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filter_name = '';
        $this->filter_cpf = '';
        $this->filter_whatsapp = '';
    }

    public function updating($property)
    {
        if (in_array($property, ['search', 'filter_name', 'filter_cpf', 'filter_whatsapp'])) {
            $this->resetPage();
        }
    }

    public function viewHistory($customerId)
    {
        $this->selectedCustomer = Customer::findOrFail($customerId);
        $this->customerHistory = Appointment::with(['service', 'user'])
            ->where('customer_id', $customerId)
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
            
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->selectedCustomer = null;
        $this->customerHistory = [];
    }

    public function render()
    {
        $query = Customer::query()->whereHas('appointments', function ($q) {
            $q->where('status', 'completed');
        });

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('cpf', 'like', '%' . $this->search . '%')
                  ->orWhere('whatsapp', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filter_name)) {
            $query->where('name', 'like', '%' . $this->filter_name . '%');
        }
        if (!empty($this->filter_cpf)) {
            $query->where('cpf', 'like', '%' . $this->filter_cpf . '%');
        }
        if (!empty($this->filter_whatsapp)) {
            $query->where('whatsapp', 'like', '%' . $this->filter_whatsapp . '%');
        }

        return view('livewire.customer-list', [
            'customers' => $query->withCount(['appointments' => function ($q) {
                $q->where('status', 'completed');
            }])->orderBy('id', 'desc')->paginate(10)
        ])->layout('components.layouts.app');
    }
}
