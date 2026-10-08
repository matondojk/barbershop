<?php

use App\Livewire\BarberAgenda;
use App\Livewire\CashierTerminal;
use App\Livewire\CollaboratorForm;
use App\Livewire\CollaboratorList;
use App\Livewire\CustomerList;
use App\Livewire\DashboardOverview;
use App\Livewire\ProductForm;
use App\Livewire\ProductList;
use App\Livewire\ProfileForm;
use App\Livewire\ServiceForm;
use App\Livewire\ServiceList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', DashboardOverview::class)->name('dashboard');
    Route::get('agenda', BarberAgenda::class)->name('agenda.index');
    Route::get('caixa', CashierTerminal::class)->name('cashier.index');

    Route::get('profile', ProfileForm::class)->name('profile.edit');

    Route::get('collaborators', CollaboratorList::class)->name('collaborators.index');
    Route::get('collaborators/create', CollaboratorForm::class)->name('collaborators.create');
    Route::get('collaborators/{user}/edit', CollaboratorForm::class)->name('collaborators.edit');

    Route::get('services', ServiceList::class)->name('services.index');
    Route::get('services/create', ServiceForm::class)->name('services.create');
    Route::get('services/{service}/edit', ServiceForm::class)->name('services.edit');

    Route::get('products', ProductList::class)->name('products.index');
    Route::get('products/create', ProductForm::class)->name('products.create');
    Route::get('products/{product}/edit', ProductForm::class)->name('products.edit');

    Route::get('customers', CustomerList::class)->name('customers.index');
    Route::get('financeiro', \App\Livewire\FinancialDashboard::class)->name('financial.index');
});
