<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Cheezious VAPI Routes ──────────────────────
use App\Http\Controllers\VapiController;

Route::get('/cheezi',          [VapiController::class, 'index']);
Route::post('/vapi/webhook',   [VapiController::class, 'webhook']);
