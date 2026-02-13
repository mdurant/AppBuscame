<?php

namespace Database\Seeders;

use App\Enums\VerificationStatus;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de prueba (credenciales en config/testing.php y en pantalla de login)
        $this->call(TestUsersSeeder::class);

        // Usuarios adicionales aleatorios para volumen
        User::factory(5)->create()->each(function (User $user) {
            Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                    'last_name' => explode(' ', $user->name)[1] ?? '',
                ]
            );
        });
    }
}
