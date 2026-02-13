<?php

namespace App\Jobs;

use App\Enums\ScoreVerificationStatus;
use App\Models\Property\Property;
use App\Models\Scoring\PropertyScore;
use App\Models\Scoring\ScoreFactor;
use App\Models\Scoring\ScoreHistory;
use App\Services\Scoring\ScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculatePropertyScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Property $property
    ) {}

    public function handle(ScoringService $scoring): void
    {
        $scoring->recalculate($this->property);
    }
}
