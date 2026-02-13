<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;

class FaqEntry extends Model
{
    protected $table = 'faq_entries';

    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'category',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
