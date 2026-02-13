<?php

use App\Http\Controllers\Web\Auth\EmailVerificationController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\OtpController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\PropertySearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PropertySearchController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/verify-email-sent', [EmailVerificationController::class, 'showSent'])->name('verify-email.sent');
    Route::get('/email/verify', [EmailVerificationController::class, 'verify'])->name('email.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('email.resend')->middleware('throttle:3,1');

    Route::get('/otp', [OtpController::class, 'showForm'])->name('otp.form');
    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend')->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/publications', [DashboardController::class, 'publications'])->name('dashboard.publications');
    Route::get('/dashboard/history', [DashboardController::class, 'history'])->name('dashboard.history');
    Route::get('/dashboard/messages', [DashboardController::class, 'messages'])->name('dashboard.messages');
    Route::get('/dashboard/payments', [DashboardController::class, 'payments'])->name('dashboard.payments');

    Route::prefix('dashboard/settings')->name('settings.')->group(function () {
        $settings = \App\Http\Controllers\Web\SettingsController::class;
        Route::get('/', [$settings, 'index'])->name('index');
        Route::get('/profile', [$settings, 'profile'])->name('profile');
        Route::put('/profile', [$settings, 'updateProfile'])->name('profile.update');
        Route::post('/account/destroy', [$settings, 'destroyAccount'])->name('account.destroy');
        Route::get('/password', [$settings, 'password'])->name('password');
        Route::put('/password', [$settings, 'updatePassword'])->name('password.update');
        Route::get('/2fa', [$settings, 'twoFactor'])->name('2fa');
        Route::post('/2fa/enable', [$settings, 'enableTwoFactor'])->name('2fa.enable');
        Route::post('/2fa/confirm', [$settings, 'confirmTwoFactor'])->name('2fa.confirm');
        Route::post('/2fa/disable', [$settings, 'disableTwoFactor'])->name('2fa.disable');
        Route::get('/sessions', [$settings, 'sessions'])->name('sessions');
        Route::delete('/sessions/{userSession}', [$settings, 'destroySession'])->name('sessions.destroy');
    });
});
