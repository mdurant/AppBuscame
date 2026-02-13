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
        $url = route('email.verify', ['token' => $token]);

        if (app()->environment('local')) {
            Cache::put('verification_url:'.$user->email, $url, now()->addMinutes(60));
            $this->logVerificationLink($user->email, $url, 'Registro');
        }

        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
    }

    /**
     * Escribe en log custom el enlace de verificación para simular envío y probar en local.
     */
    private function logVerificationLink(string $email, string $url, string $origen): void
    {
        $message = sprintf(
            "\n========== ENLACE DE VERIFICACIÓN (%s) ==========\nCorreo: %s\nEnlace (copiar y abrir en el navegador):\n%s\n==========================================\n",
            $origen,
            $email,
            $url
        );
        Log::channel('verification')->info($message);
        Log::channel('single')->info('[VERIFICATION] '.$origen.' - '.$email, ['url' => $url]);
    }
}
