<?php

namespace App\Services\Scoring;

use App\Enums\ScoreVerificationStatus;
use App\Models\Property\Property;
use App\Models\Scoring\PropertyScore;
use App\Models\Scoring\ScoreFactor;
use App\Models\Scoring\ScoreHistory;

class ScoringService
{
    /**
     * Factores y pesos (completitud, dirección, fotos, etc.)
     */
    private const FACTORS = [
        'completeness' => 0.30,
        'address_validated' => 0.25,
        'photos_count' => 0.20,
        'has_availability' => 0.15,
        'reputation' => 0.10,
    ];

    public function recalculate(Property $property): PropertyScore
    {
        $scoreRecord = $property->score ?? PropertyScore::create([
            'property_id' => $property->id,
            'overall_score' => 0,
            'verification_status' => ScoreVerificationStatus::Unverified,
        ]);

        $previousScore = $scoreRecord->overall_score;
        $factors = $this->calculateFactors($property);
        $overall = $this->weightedScore($factors);

        $scoreRecord->factors()->delete();
        foreach ($factors as $key => $value) {
            $weight = self::FACTORS[$key] ?? 0.10;
            ScoreFactor::create([
                'property_score_id' => $scoreRecord->id,
                'factor_key' => $key,
                'weight' => $weight,
                'value' => $value,
            ]);
        }

        $scoreRecord->update([
            'overall_score' => $overall,
            'verification_status' => $this->deriveVerificationStatus($overall, $factors),
            'last_calculated_at' => now(),
        ]);

        ScoreHistory::create([
            'property_id' => $property->id,
            'previous_score' => $previousScore,
            'new_score' => $overall,
            'factors_snapshot' => $factors,
            'calculated_at' => now(),
        ]);

        return $scoreRecord;
    }

    private function calculateFactors(Property $property): array
    {
        $completeness = min(100, (float) $property->completeness_percent) / 100;
        $addressValidated = $property->address && $property->address->latitude && $property->address->longitude ? 1.0 : 0.0;
        $photosCount = min(5, $property->photos()->count()) / 5;
        $hasAvailability = $property->availabilityRanges()->exists() ? 1.0 : 0.0;
        $reputation = 0.5; // Placeholder: podría ser valor histórico o reseñas

        return [
            'completeness' => $completeness,
            'address_validated' => $addressValidated,
            'photos_count' => $photosCount,
            'has_availability' => $hasAvailability,
            'reputation' => $reputation,
        ];
    }

    private function weightedScore(array $factors): float
    {
        $total = 0.0;
        foreach (self::FACTORS as $key => $weight) {
            $total += ($factors[$key] ?? 0) * $weight;
        }

        return round($total * 100, 2);
    }

    private function deriveVerificationStatus(float $score, array $factors): ScoreVerificationStatus
    {
        if ($score >= 80 && ($factors['address_validated'] ?? 0) >= 0.99) {
            return ScoreVerificationStatus::Verified;
        }
        if ($score >= 50) {
            return ScoreVerificationStatus::Pending;
        }

        return ScoreVerificationStatus::Unverified;
    }
}
