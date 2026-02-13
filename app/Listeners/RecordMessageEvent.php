<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\Messaging\MessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordMessageEvent implements ShouldQueue
{
    public function handle(MessageSent $event): void
    {
        MessageEvent::create([
            'message_id' => $event->message->id,
            'event_type' => 'sent',
            'user_id' => $event->message->user_id,
            'payload' => [],
            'created_at' => now(),
        ]);
    }
}
