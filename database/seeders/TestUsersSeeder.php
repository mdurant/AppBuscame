<?php

namespace Database\Seeders;

use App\Enums\VerificationStatus;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios de prueba para testing técnico.
 * Credenciales: ver config/testing.php (y pantalla de login en entorno local).
 */
class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(config('testing.password', 'Password1!'));

        $users = [
            [
                'name' => 'María González',
                'email' => 'maria@ejemplo.cl',
                'verification_status' => VerificationStatus::FullyVerified,
                'first_name' => 'María',
                'last_name' => 'González',
            ],
            [
                'name' => 'Carlos Rojas',
                'email' => 'carlos@ejemplo.cl',
                'verification_status' => VerificationStatus::FullyVerified,
                'first_name' => 'Carlos',
                'last_name' => 'Rojas',
            ],
            [
                'name' => 'Usuario Test',
                'email' => 'test@ejemplo.cl',
                'verification_status' => VerificationStatus::FullyVerified,
                'first_name' => 'Usuario',
                'last_name' => 'Test',
            ],
        ];

        foreach ($users as $data) {
            $firstName = $data['first_name'];
            $lastName = $data['last_name'];
            $email = $data['email'];
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'verification_status' => $data['verification_status'],
                    'email_verified_at' => now(),
                ]
            );
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                ['first_name' => $firstName, 'last_name' => $lastName]
            );
        }
    }
}
