<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CabinetController;
use App\Http\Controllers\Api\PatientController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Route::middleware('auth:sanctum')->group(function () {
    // Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('tickets/{ticket}/call', [TicketController::class, 'call']);
    Route::post('tickets/{ticket}/status', [TicketController::class, 'markStatus']);
    Route::post('tickets/{ticket}/recall', [TicketController::class, 'recall']);
    Route::apiResource('tickets', TicketController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('cabinets', CabinetController::class);
    Route::apiResource('patients', PatientController::class);
// });