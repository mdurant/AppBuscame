<?php

namespace App\Providers;

use App\Events\EmailVerified;
use App\Events\MessageSent;
use App\Events\OtpRequested;
use App\Events\PropertyPublished;
use App\Events\TwoFactorEnabled;
use App\Events\UserRegistered;
use App\Listeners\RecalculatePropertyScore;
use App\Listeners\RecordMessageEvent;
use App\Listeners\SendBackupCodesEmail;
use App\Listeners\SendOtpEmail;
use App\Listeners\SendVerificationEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(\App\Models\Property\Property::class, \App\Policies\PropertyPolicy::class);

        Event::listen(UserRegistered::class, SendVerificationEmail::class);
        Event::listen(OtpRequested::class, SendOtpEmail::class);
        Event::listen(TwoFactorEnabled::class, SendBackupCodesEmail::class);
        Event::listen(PropertyPublished::class, RecalculatePropertyScore::class);
        Event::listen(MessageSent::class, RecordMessageEvent::class);
    }
}
