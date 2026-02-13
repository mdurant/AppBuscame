<?php

namespace Database\Seeders;

use App\Models\Property\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'BuscaMe', 'slug' => 'buscame', 'type' => 'platform', 'url' => null],
            ['name' => 'Booking', 'slug' => 'booking', 'type' => 'api', 'url' => 'https://www.booking.com'],
            ['name' => 'Arrienda Apartamentos', 'slug' => 'arrienda_apartamentos', 'type' => 'api', 'url' => null],
            ['name' => 'Latam Airlines', 'slug' => 'latam_airline', 'type' => 'api', 'url' => null],
            ['name' => 'Redes sociales', 'slug' => 'social', 'type' => 'social', 'url' => null],
        ];

        foreach ($sources as $data) {
            Source::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
