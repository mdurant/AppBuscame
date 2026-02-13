<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TermsVersionSeeder::class,
            SourceSeeder::class,
            UserSeeder::class,
            PropertySeeder::class,
            FaqSeeder::class,
        ]);
    }
}
