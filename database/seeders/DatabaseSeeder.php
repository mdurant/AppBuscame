<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SourceSeeder::class,
            UserSeeder::class,
            AdminPlatformSeeder::class,
            DummyUsersChileSeeder::class,
            PropertySeeder::class,
            FaqSeeder::class,
        ]);
    }
}
