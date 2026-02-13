<?php

namespace App\Listeners;

use App\Events\TwoFactorEnabled;
use App\Mail\BackupCodesMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBackupCodesEmail implements ShouldQueue
{
    public function handle(TwoFactorEnabled $event): void
    {
        Mail::to($event->user->email)->send(new BackupCodesMail($event->user, $event->backupCodes));
    }
}
