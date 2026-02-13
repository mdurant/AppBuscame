<?php

namespace App\Models\Scoring;

use App\Enums\ScoreVerificationStatus;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyScore extends Model
{
    protected $table = 'property_scores';

    protected $fillable = [
        'property_id',
        'overall_score',
        'verification_status',
        'last_calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'overall_score' => 'decimal:2',
            'last_calculated_at' => 'datetime',
            'verification_status' => ScoreVerificationStatus::class,
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function factors(): HasMany
    {
        return $this->hasMany(ScoreFactor::class, 'property_score_id');
    }
}
