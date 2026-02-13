<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TermsVersion extends Model
{
    protected $table = 'terms_versions';

    protected $fillable = [
        'version',
        'content',
        'effective_at',
    ];

    protected function casts(): array
    {
        return [
            'effective_at' => 'datetime',
        ];
    }

    public function acceptances(): HasMany
    {
        return $this->hasMany(UserTermAcceptance::class, 'terms_version_id');
    }
}
