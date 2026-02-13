<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Dashboard\PropertyController as DashboardPropertyController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user()?->load('profile');
})->middleware('auth:sanctum');

// Auth (público, rate limited en middleware)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', RegisterController::class);
});

Route::middleware('throttle:5,1')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'verify']);
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->middleware('throttle:3,1');
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/otp/resend', [OtpController::class, 'resend'])->middleware('throttle:3,1');
});

// Propiedades públicas
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

// Dashboard (auth:sanctum)
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('properties', [DashboardPropertyController::class, 'index']);
    Route::post('properties', [DashboardPropertyController::class, 'store']);
    Route::post('properties/{property}/publish', [DashboardPropertyController::class, 'publish']);
    Route::get('properties/{property}', [DashboardPropertyController::class, 'show']);
    Route::put('properties/{property}', [DashboardPropertyController::class, 'update']);
});
