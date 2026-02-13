<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use App\Mail\OtpCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOtpEmail implements ShouldQueue
{
    public function handle(OtpRequested $event): void
    {
        Mail::to($event->user->email)->send(new OtpCodeMail($event->user, $event->code));
    }
}
