<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', \App\Livewire\DashboardOverview::class)->name('dashboard');
    Route::get('agenda', \App\Livewire\BarberAgenda::class)->name('agenda.index');
    Route::get('caixa', \App\Livewire\CashierTerminal::class)->name('cashier.index');
    
    Route::get('profile', \App\Livewire\ProfileForm::class)->name('profile.edit');

    Route::get('collaborators', \App\Livewire\CollaboratorList::class)->name('collaborators.index');
    Route::get('collaborators/create', \App\Livewire\CollaboratorForm::class)->name('collaborators.create');
    Route::get('collaborators/{user}/edit', \App\Livewire\CollaboratorForm::class)->name('collaborators.edit');

    Route::get('services', \App\Livewire\ServiceList::class)->name('services.index');
    Route::get('services/create', \App\Livewire\ServiceForm::class)->name('services.create');
    Route::get('services/{service}/edit', \App\Livewire\ServiceForm::class)->name('services.edit');
});
