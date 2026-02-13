<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\VerifyEmailMail;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
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

        Mail::to($user->email)->send(new VerifyEmailMail($user, $url));
    }
}
