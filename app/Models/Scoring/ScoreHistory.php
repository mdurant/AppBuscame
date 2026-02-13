<?php

namespace App\Models\Scoring;

use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScoreHistory extends Model
{
    protected $table = 'score_history';

    public $timestamps = true;

    protected $fillable = [
        'property_id',
        'previous_score',
        'new_score',
        'factors_snapshot',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_score' => 'decimal:2',
            'new_score' => 'decimal:2',
            'factors_snapshot' => 'array',
            'calculated_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
