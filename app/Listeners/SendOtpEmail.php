<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use App\Mail\OtpCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    public function handle(OtpRequested $event): void
    {
        Log::channel('single')->info('OTP enviado por correo (sin motor de correo)', [
            'email' => $event->user->email,
            'code' => $event->code,
        ]);

        Mail::to($event->user->email)->send(new OtpCodeMail($event->user, $event->code));
    }
}
