<?php

namespace App\Listeners;

use App\Events\PropertyPublished;
use App\Jobs\CalculatePropertyScoreJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecalculatePropertyScore implements ShouldQueue
{
    public function handle(PropertyPublished $event): void
    {
        CalculatePropertyScoreJob::dispatch($event->property);
    }
}
