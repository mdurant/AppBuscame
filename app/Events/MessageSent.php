<?php

namespace App\Events;

use App\Models\Messaging\Message;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Message $message
    ) {}
}
