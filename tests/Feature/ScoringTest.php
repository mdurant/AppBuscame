<?php

namespace Tests\Feature;

use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyPhoto;
use App\Models\User;
use App\Services\Scoring\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_recalculate_crea_property_score_e_historial(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->draft()->create([
            'user_id' => $user->id,
            'completeness_percent' => 80,
        ]);
        PropertyAddress::create([
            'property_id' => $property->id,
            'address_line' => 'Calle 1',
            'city' => 'Santiago',
            'latitude' => -33.45,
            'longitude' => -70.65,
        ]);
        PropertyPhoto::create(['property_id' => $property->id, 'path' => 'p/1.jpg', 'sort_order' => 0]);

        $service = app(ScoringService::class);
        $score = $service->recalculate($property);

        $this->assertNotNull($score->overall_score);
        $this->assertGreaterThanOrEqual(0, $score->overall_score);
        $this->assertLessThanOrEqual(100, $score->overall_score);
        $this->assertDatabaseHas('score_history', ['property_id' => $property->id]);
        $this->assertTrue($score->factors()->exists());
    }
}
