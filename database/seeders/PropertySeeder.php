<?php

namespace Database\Seeders;

use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\Source;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('email', 'maria@ejemplo.cl')->first();
        $sourceBuscaMe = Source::where('slug', 'buscame')->first();
        if (! $owner || ! $sourceBuscaMe) {
            return;
        }

        Property::factory(3)->create(['user_id' => $owner->id, 'source_id' => $sourceBuscaMe->id])->each(function (Property $property) {
            PropertyAddress::create([
                'property_id' => $property->id,
                'address_line' => 'Av. Providencia ' . $property->id . '234',
                'city' => 'Santiago',
                'region' => 'Metropolitana',
                'country' => 'CL',
                'latitude' => -33.4372 + (rand(-100, 100) / 10000),
                'longitude' => -70.6506 + (rand(-100, 100) / 10000),
            ]);
        });
    }
}
