<?php

namespace Tests\Feature;

use App\Models\Messaging\Message;
use App\Models\Messaging\MessageThread;
use App\Models\Messaging\ThreadParticipant;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_mensaje_registra_evento_sent(): void
    {
        Event::fake([\App\Events\MessageSent::class]);
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $thread = MessageThread::create(['property_id' => $property->id]);
        ThreadParticipant::create(['message_thread_id' => $thread->id, 'user_id' => $user->id, 'role' => 'owner']);
        $other = User::factory()->create();
        ThreadParticipant::create(['message_thread_id' => $thread->id, 'user_id' => $other->id, 'role' => 'renter']);

        $message = Message::create([
            'message_thread_id' => $thread->id,
            'user_id' => $other->id,
            'body' => 'Hola, me interesa la propiedad.',
        ]);

        event(new \App\Events\MessageSent($message));

        Event::assertDispatched(\App\Events\MessageSent::class);
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'body' => 'Hola, me interesa la propiedad.']);
    }

    public function test_listener_registra_message_event(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['user_id' => $user->id]);
        $thread = MessageThread::create(['property_id' => $property->id]);
        ThreadParticipant::create(['message_thread_id' => $thread->id, 'user_id' => $user->id, 'role' => 'owner']);
        $message = Message::create([
            'message_thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' => 'Test',
        ]);

        event(new \App\Events\MessageSent($message));

        $this->assertDatabaseHas('message_events', [
            'message_id' => $message->id,
            'event_type' => 'sent',
        ]);
    }
}
