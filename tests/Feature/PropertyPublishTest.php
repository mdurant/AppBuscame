<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Billing\Subscription;
use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PropertyPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_listado_publico_solo_muestra_published(): void
    {
        $user = User::factory()->create();
        Property::factory()->draft()->create(['user_id' => $user->id]);
        Property::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(PropertyStatus::Published->value, $data[0]['status']);
    }

    public function test_publicar_sin_suscripcion_devuelve_402(): void
    {
        Event::fake([\App\Events\PropertyPublished::class]);
        $user = User::factory()->create();
        $property = Property::factory()->draft()->create([
            'user_id' => $user->id,
            'completeness_percent' => 100,
        ]);
        PropertyAddress::create([
            'property_id' => $property->id,
            'address_line' => 'Calle 1',
            'city' => 'Santiago',
            'latitude' => -33.45,
            'longitude' => -70.65,
        ]);
        PropertyPhoto::create(['property_id' => $property->id, 'path' => 'photos/1.jpg', 'sort_order' => 0]);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withToken($token)->postJson("/api/dashboard/properties/{$property->id}/publish");

        $response->assertStatus(402);
        $response->assertJsonPath('redirect_to_payment', true);
        $property->refresh();
        $this->assertEquals(PropertyStatus::PendingPayment, $property->status);
        Event::assertNotDispatched(\App\Events\PropertyPublished::class);
    }

    public function test_publicar_con_suscripcion_activa_cambia_a_published(): void
    {
        Event::fake([\App\Events\PropertyPublished::class]);
        $user = User::factory()->create();
        Subscription::create([
            'user_id' => $user->id,
            'plan_slug' => 'basico',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);
        $property = Property::factory()->draft()->create([
            'user_id' => $user->id,
            'completeness_percent' => 100,
        ]);
        PropertyAddress::create([
            'property_id' => $property->id,
            'address_line' => 'Calle 1',
            'city' => 'Santiago',
            'latitude' => -33.45,
            'longitude' => -70.65,
        ]);
        PropertyPhoto::create(['property_id' => $property->id, 'path' => 'photos/1.jpg', 'sort_order' => 0]);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withToken($token)->postJson("/api/dashboard/properties/{$property->id}/publish");

        $response->assertStatus(200);
        $property->refresh();
        $this->assertEquals(PropertyStatus::Published, $property->status);
        $this->assertNotNull($property->published_at);
        Event::assertDispatched(\App\Events\PropertyPublished::class);
    }
}
