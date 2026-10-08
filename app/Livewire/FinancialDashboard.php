<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinancialDashboard extends Component
{
    public $period = 'month'; // today, week, month, year, all

    public function mount()
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Acesso restrito a administradores.');
    }

    public function setPeriod($newPeriod)
    {
        $this->period = $newPeriod;
    }

    public function getStatsProperty()
    {
        $query = Appointment::where('status', 'completed');

        // Apply period filter
        if ($this->period === 'today') {
            $query->whereDate('updated_at', Carbon::today());
        } elseif ($this->period === 'week') {
            $query->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->period === 'month') {
            $query->whereMonth('updated_at', Carbon::now()->month)
                  ->whereYear('updated_at', Carbon::now()->year);
        } elseif ($this->period === 'year') {
            $query->whereYear('updated_at', Carbon::now()->year);
        }

        // We clone the query for aggregate totals so we don't fetch everything into memory if it's large
        $totalRevenue = (clone $query)->sum('amount_paid');
        $totalCommission = (clone $query)->sum('commission_amount');
        $totalDiscounts = (clone $query)->sum('discount_amount');
        $netProfit = $totalRevenue - $totalCommission;

        // Appointments data
        $appointments = $query->with(['service', 'products'])->get();
        $totalAppointments = $appointments->count();

        // Calculate split between Services and Products
        $servicesRevenue = 0;
        $productsRevenue = 0;
        $servicesCount = [];
        $productsCount = [];

        foreach ($appointments as $appt) {
            // Service revenue calculation
            $servicesRevenue += $appt->service->price;
            
            // Service stats
            if (!isset($servicesCount[$appt->service->name])) {
                $servicesCount[$appt->service->name] = 0;
            }
            $servicesCount[$appt->service->name]++;

            // Products revenue and stats
            foreach ($appt->products as $product) {
                $itemTotal = $product->pivot->unit_price * $product->pivot->quantity;
                $productsRevenue += $itemTotal;

                if (!isset($productsCount[$product->name])) {
                    $productsCount[$product->name] = 0;
                }
                $productsCount[$product->name] += $product->pivot->quantity;
            }
        }

        // Sort best sellers
        arsort($servicesCount);
        arsort($productsCount);

        return [
            'totalRevenue' => $totalRevenue,
            'totalCommission' => $totalCommission,
            'totalDiscounts' => $totalDiscounts,
            'netProfit' => $netProfit,
            'servicesRevenue' => $servicesRevenue,
            'productsRevenue' => $productsRevenue,
            'totalAppointments' => $totalAppointments,
            'topServices' => array_slice($servicesCount, 0, 5),
            'topProducts' => array_slice($productsCount, 0, 5),
        ];
    }

    public function render()
    {
        return view('livewire.financial-dashboard', [
            'stats' => $this->stats
        ])->layout('components.layouts.app');
    }
}
