<?php

namespace App\Models\Messaging;

use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageThread extends Model
{
    protected $table = 'message_threads';

    protected $fillable = ['property_id'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ThreadParticipant::class, 'message_thread_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'message_thread_id')->orderBy('created_at');
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thread_participants', 'message_thread_id', 'user_id')
            ->withPivot('role')
            ->withTimestamps();
    }
}
