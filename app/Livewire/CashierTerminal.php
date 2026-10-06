<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Component;

class CashierTerminal extends Component
{
    public $searchCpf = '';
    
    // Payment Modal State
    public $checkoutAppointment = null;
    public $payment_method = 'pix';
    public $amount_paid = '';
    public $change_amount = 0;

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso restrito ao Caixa/Financeiro.');
    }

    public function openCheckout($id)
    {
        $this->checkoutAppointment = Appointment::with(['customer', 'user', 'service'])->findOrFail($id);
        $this->amount_paid = number_format($this->checkoutAppointment->service->price, 2, ',', '.');
        $this->calculateChange();
    }

    public function closeCheckout()
    {
        $this->checkoutAppointment = null;
        $this->payment_method = 'pix';
        $this->amount_paid = '';
        $this->change_amount = 0;
    }

    public function updatedAmountPaid()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        if (!$this->checkoutAppointment) return;
        
        $cleanAmount = (float) str_replace(['.', ','], ['', '.'], $this->amount_paid);
        $servicePrice = (float) $this->checkoutAppointment->service->price;
        
        $this->change_amount = max(0, $cleanAmount - $servicePrice);
    }

    public function confirmPayment()
    {
        if (!$this->checkoutAppointment) return;

        $cleanAmountPaid = (float) str_replace(['.', ','], ['', '.'], $this->amount_paid);
        $servicePrice = (float) $this->checkoutAppointment->service->price;

        if ($cleanAmountPaid < $servicePrice) {
            $this->addError('amount_paid', 'O valor pago é menor que o valor do serviço.');
            return;
        }

        // Calculate Commission
        $barber = $this->checkoutAppointment->user;
        $commissionAmount = 0;
        
        if ($barber->commission_type === 'percentage') {
            $commissionAmount = $servicePrice * ($barber->commission_value / 100);
        } else {
            $commissionAmount = $barber->commission_value;
        }

        $this->checkoutAppointment->update([
            'status' => 'completed',
            'payment_method' => $this->payment_method,
            'amount_paid' => $cleanAmountPaid,
            'change_amount' => $this->change_amount,
            'commission_amount' => $commissionAmount,
        ]);

        session()->flash('success', 'Pagamento recebido e comissão lançada com sucesso!');
        $this->closeCheckout();
    }

    public function render()
    {
        $query = Appointment::with(['customer', 'user', 'service'])
            ->whereIn('status', ['awaiting_payment', 'in_progress'])
            ->orderBy('updated_at', 'desc');

        if (!empty($this->searchCpf)) {
            $cleanCpf = preg_replace('/[^0-9]/', '', $this->searchCpf);
            $query->whereHas('customer', function($q) use ($cleanCpf) {
                $q->where('cpf', 'like', "%{$cleanCpf}%");
            });
        }

        $pendingPayments = $query->get();

        return view('livewire.cashier-terminal', [
            'pendingPayments' => $pendingPayments
        ])->layout('components.layouts.app');
    }
}
