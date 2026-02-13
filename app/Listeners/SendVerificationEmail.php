<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\VerifyEmailMail;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVerificationEmail implements ShouldQueue
{
    public function __construct(
        protected EmailVerificationService $emailVerification
    ) {}

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $token = $this->emailVerification->createVerification($user);
        $url = url('/email/verify?token='.$token);

        Log::channel('single')->info('Verificación de correo (sin motor de correo)', [
            'email' => $user->email,
            'verification_url' => $url,
        ]);

        if (app()->environment('local')) {
            Cache::put('verification_url:'.$user->email, $url, now()->addMinutes(60));
        }

        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
    }
}
