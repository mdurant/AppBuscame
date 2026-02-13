<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'url',
        'logo_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'source_id');
    }

    public function getBadgeColorAttribute(): string
    {
        return match ($this->slug) {
            'platform', 'buscame' => 'indigo',
            'booking' => 'blue',
            'arrienda_apartamentos' => 'emerald',
            'latam_airline', 'latam' => 'red',
            'social' => 'violet',
            default => 'gray',
        };
    }
}
