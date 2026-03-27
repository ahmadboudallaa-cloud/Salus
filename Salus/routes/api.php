<?php

use App\Http\Controllers\Api\AiHealthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\SymptomController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('doctors', [DoctorController::class, 'index']);
Route::get('doctors/search', [DoctorController::class, 'search']);
Route::get('doctors/{id}', [DoctorController::class, 'show'])->whereNumber('id');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('symptoms', [SymptomController::class, 'index']);
    Route::post('symptoms', [SymptomController::class, 'store']);
    Route::get('symptoms/{id}', [SymptomController::class, 'show'])->whereNumber('id');
    Route::put('symptoms/{id}', [SymptomController::class, 'update'])->whereNumber('id');
    Route::delete('symptoms/{id}', [SymptomController::class, 'destroy'])->whereNumber('id');

    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('appointments/{id}', [AppointmentController::class, 'show'])->whereNumber('id');
    Route::put('appointments/{id}', [AppointmentController::class, 'update'])->whereNumber('id');
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy'])->whereNumber('id');

    Route::post('ai/health-advice', [AiHealthController::class, 'generate']);
    Route::get('ai/health-advice/history', [AiHealthController::class, 'history']);
});
