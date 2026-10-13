<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp');
