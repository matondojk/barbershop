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
    $services = \App\Models\Service::where('is_active', true)->orderBy('name')->get();
    return view('welcome', compact('services'));
});

Route::get('/agendar', function () {
    return view('scheduling');
})->name('scheduling');

Route::get('/agendamento/{hash}', \App\Livewire\ManageAppointment::class)->name('appointment.manage');

Route::middleware(['auth'])->prefix('painel')->group(function () {
    Route::get('/', DashboardOverview::class)->name('dashboard');
    Route::get('agenda', BarberAgenda::class)->name('agenda.index');
    Route::get('caixa', CashierTerminal::class)->name('cashier.index');

    Route::get('perfil', ProfileForm::class)->name('profile.edit');

    Route::get('equipe', CollaboratorList::class)->name('collaborators.index');
    Route::get('equipe/novo', CollaboratorForm::class)->name('collaborators.create');
    Route::get('equipe/{user}/editar', CollaboratorForm::class)->name('collaborators.edit');

    Route::get('servicos', ServiceList::class)->name('services.index');
    Route::get('servicos/novo', ServiceForm::class)->name('services.create');
    Route::get('servicos/{service}/editar', ServiceForm::class)->name('services.edit');

    Route::get('produtos', ProductList::class)->name('products.index');
    Route::get('produtos/novo', ProductForm::class)->name('products.create');
    Route::get('produtos/{product}/editar', ProductForm::class)->name('products.edit');

    Route::get('clientes', CustomerList::class)->name('customers.index');
    Route::get('financeiro', \App\Livewire\FinancialDashboard::class)->name('financial.index');
    
    Route::get('whatsapp/novo', \App\Livewire\WhatsappCreate::class)->name('whatsapp.create');
    Route::get('whatsapp', \App\Livewire\WhatsappManager::class)->name('whatsapp.index');
});
