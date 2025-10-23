<?php

use Illuminate\Support\Facades\Route;

// Example default route
Route::get('/ping', function () {
    return response()->json(['message' => 'API is working!']);
});

use App\Http\Controllers\MpesaController;

Route::post('/mpesa/validate', [MpesaController::class, 'validatePayment']);
Route::post('/mpesa/confirm', [MpesaController::class, 'confirmPayment']);


Route::post('/sms/delivery-report', [\App\Http\Controllers\SmsDeliveryController::class, 'receive']);