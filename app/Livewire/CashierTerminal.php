<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Product;
use Livewire\Component;

class CashierTerminal extends Component
{
    public $searchCpf = '';

    // Payment Modal State
    public $checkoutAppointment = null;

    public $payment_method = 'pix';

    public $amount_paid = '';

    public $change_amount = 0;

    public $discount_amount = '';

    // Products State
    public $availableProducts = [];

    public $selectedProduct = '';

    public $selectedProductsList = []; // Format: [['product_id' => id, 'name' => name, 'price' => price, 'quantity' => 1]]

    public function mount()
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Acesso restrito ao Caixa/Financeiro.');
    }

    public function openCheckout($id)
    {
        $this->checkoutAppointment = Appointment::with(['customer', 'user', 'service'])->findOrFail($id);

        $this->availableProducts = Product::where('is_active', true)->where('stock_quantity', '>', 0)->get();
        $this->selectedProduct = '';
        $this->selectedProductsList = [];
        $this->discount_amount = '';

        // Default amount paid is just the service price initially
        $this->amount_paid = number_format($this->checkoutAppointment->service->price, 2, ',', '.');
        $this->calculateChange();
    }

    public function addProduct()
    {
        if (empty($this->selectedProduct)) {
            return;
        }

        $product = $this->availableProducts->firstWhere('id', $this->selectedProduct);
        if (! $product) {
            return;
        }

        // Check if already exists in list
        $index = collect($this->selectedProductsList)->search(fn ($item) => $item['product_id'] == $product->id);

        if ($index !== false) {
            if ($this->selectedProductsList[$index]['quantity'] < $product->stock_quantity) {
                $this->selectedProductsList[$index]['quantity']++;
            } else {
                $this->addError('selectedProduct', 'Estoque insuficiente.');

                return;
            }
        } else {
            $this->selectedProductsList[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
            ];
        }

        $this->selectedProduct = '';
        $this->updateExpectedAmount();
    }

    public function removeProduct($index)
    {
        if (isset($this->selectedProductsList[$index])) {
            unset($this->selectedProductsList[$index]);
            $this->selectedProductsList = array_values($this->selectedProductsList); // Reindex
            $this->updateExpectedAmount();
        }
    }

    public function increaseQuantity($index)
    {
        if (isset($this->selectedProductsList[$index])) {
            $product = $this->availableProducts->firstWhere('id', $this->selectedProductsList[$index]['product_id']);
            if ($this->selectedProductsList[$index]['quantity'] < $product->stock_quantity) {
                $this->selectedProductsList[$index]['quantity']++;
                $this->updateExpectedAmount();
            }
        }
    }

    public function decreaseQuantity($index)
    {
        if (isset($this->selectedProductsList[$index])) {
            if ($this->selectedProductsList[$index]['quantity'] > 1) {
                $this->selectedProductsList[$index]['quantity']--;
                $this->updateExpectedAmount();
            } else {
                $this->removeProduct($index);
            }
        }
    }

    public function updateExpectedAmount()
    {
        $expected = $this->getExpectedTotal();
        $this->amount_paid = number_format($expected, 2, ',', '.');
        $this->calculateChange();
    }

    public function getExpectedTotal()
    {
        if (! $this->checkoutAppointment) {
            return 0;
        }

        $servicePrice = (float) $this->checkoutAppointment->service->price;

        $productsTotal = 0;
        foreach ($this->selectedProductsList as $item) {
            $productsTotal += ($item['price'] * $item['quantity']);
        }

        $discount = (float) str_replace(['.', ','], ['', '.'], $this->discount_amount ?: '0');

        return max(0, $servicePrice + $productsTotal - $discount);
    }

    public function closeCheckout()
    {
        $this->checkoutAppointment = null;
        $this->payment_method = 'pix';
        $this->amount_paid = '';
        $this->change_amount = 0;
        $this->discount_amount = '';
        $this->selectedProductsList = [];
    }

    public function updatedAmountPaid()
    {
        $this->calculateChange();
    }

    public function updatedDiscountAmount()
    {
        $this->updateExpectedAmount();
    }

    public function calculateChange()
    {
        if (! $this->checkoutAppointment) {
            return;
        }

        $cleanAmount = (float) str_replace(['.', ','], ['', '.'], $this->amount_paid);
        $expectedTotal = $this->getExpectedTotal();

        $this->change_amount = max(0, $cleanAmount - $expectedTotal);
    }

    public function confirmPayment()
    {
        if (! $this->checkoutAppointment) {
            return;
        }

        $cleanAmountPaid = (float) str_replace(['.', ','], ['', '.'], $this->amount_paid);
        $discountAmount = (float) str_replace(['.', ','], ['', '.'], $this->discount_amount ?: '0');
        $expectedTotal = $this->getExpectedTotal();

        if ($cleanAmountPaid < $expectedTotal) {
            $this->addError('amount_paid', 'O valor pago é menor que o valor total.');

            return;
        }

        // Calculate Commission (ONLY ON SERVICE PRICE)
        $servicePrice = (float) $this->checkoutAppointment->service->price;
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
            'discount_amount' => $discountAmount,
            'commission_amount' => $commissionAmount,
        ]);

        // Attach products and deduct stock
        $syncData = [];
        foreach ($this->selectedProductsList as $item) {
            $syncData[$item['product_id']] = [
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ];

            // Deduct stock
            $product = Product::find($item['product_id']);
            if ($product) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        if (! empty($syncData)) {
            $this->checkoutAppointment->products()->sync($syncData);
        }

        session()->flash('success', 'Pagamento recebido e comissão lançada com sucesso!');
        $this->closeCheckout();
    }

    public function render()
    {
        $query = Appointment::with(['customer', 'user', 'service'])
            ->whereIn('status', ['awaiting_payment', 'in_progress'])
            ->orderBy('updated_at', 'desc');

        if (! empty($this->searchCpf)) {
            $cleanCpf = preg_replace('/[^0-9]/', '', $this->searchCpf);
            $query->whereHas('customer', function ($q) use ($cleanCpf) {
                $q->where('cpf', 'like', "%{$cleanCpf}%");
            });
        }

        $pendingPayments = $query->get();

        return view('livewire.cashier-terminal', [
            'pendingPayments' => $pendingPayments,
        ])->layout('components.layouts.app');
    }
}
