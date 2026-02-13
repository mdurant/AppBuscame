<?php

namespace App\Models\Scoring;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreFactor extends Model
{
    protected $table = 'score_factors';

    protected $fillable = [
        'property_score_id',
        'factor_key',
        'weight',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'value' => 'decimal:2',
        ];
    }

    public function propertyScore(): BelongsTo
    {
        return $this->belongsTo(PropertyScore::class);
    }
}
