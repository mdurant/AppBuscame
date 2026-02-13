<?php

namespace App\Models\Property;

use App\Enums\PropertyStatus;
use App\Models\Scoring\PropertyScore;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\PropertyFactory
    {
        return \Database\Factories\PropertyFactory::new();
    }

    protected $fillable = [
        'user_id',
        'source_id',
        'type',
        'status',
        'rental_type',
        'cost_amount',
        'cost_currency',
        'check_in_time',
        'check_out_time',
        'includes_cleaning',
        'completeness_percent',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'includes_cleaning' => 'boolean',
            'completeness_percent' => 'integer',
            'cost_amount' => 'decimal:2',
            'published_at' => 'datetime',
            'status' => PropertyStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(PropertyAddress::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    public function availabilityRanges(): HasMany
    {
        return $this->hasMany(AvailabilityRange::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(PropertyScore::class);
    }

    public function isDraft(): bool
    {
        return $this->status === PropertyStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this->status === PropertyStatus::Published;
    }
}
