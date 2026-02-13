<?php

namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Models\Property\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['casa', 'departamento', 'habitacion', 'estudio']),
            'status' => PropertyStatus::Published,
            'rental_type' => 'mensual',
            'cost_amount' => fake()->numberBetween(200000, 800000),
            'cost_currency' => 'CLP',
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
            'includes_cleaning' => fake()->boolean(40),
            'completeness_percent' => 100,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PropertyStatus::Draft,
            'published_at' => null,
            'completeness_percent' => fake()->numberBetween(0, 80),
        ]);
    }
}
