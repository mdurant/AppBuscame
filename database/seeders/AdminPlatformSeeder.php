<?php

namespace Database\Seeders;

use App\Enums\VerificationStatus;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea los 3 perfiles de usuario de la plataforma:
 * - admin@integraltech.cl (Admin)
 * - cliente@integraltech.cl (Cliente)
 * - usuario@integraltech.cl (Usuario)
 */
class AdminPlatformSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password1!');

        $accounts = [
            [
                'name' => 'Admin Integraltech',
                'email' => 'admin@integraltech.cl',
                'role' => 'admin',
                'first_name' => 'Admin',
                'last_name' => 'Integraltech',
            ],
            [
                'name' => 'Cliente Integraltech',
                'email' => 'cliente@integraltech.cl',
                'role' => 'cliente',
                'first_name' => 'Cliente',
                'last_name' => 'Integraltech',
            ],
            [
                'name' => 'Usuario Integraltech',
                'email' => 'usuario@integraltech.cl',
                'role' => 'usuario',
                'first_name' => 'Usuario',
                'last_name' => 'Integraltech',
            ],
        ];

        foreach ($accounts as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'verification_status' => VerificationStatus::FullyVerified,
                    'email_verified_at' => now(),
                    'role' => $data['role'],
                ]
            );
            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'region' => 'Metropolitana',
                    'city' => 'Santiago',
                ]
            );
        }
    }
}
