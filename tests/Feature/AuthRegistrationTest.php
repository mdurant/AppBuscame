<?php

namespace Tests\Feature;

use App\Enums\VerificationStatus;
use App\Models\User;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake([\App\Events\UserRegistered::class]);
    }

    public function test_registro_crea_usuario_y_envia_evento(): void
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan@test.cl',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('user.email', 'juan@test.cl');
        $response->assertJsonPath('user.verification_status', VerificationStatus::RegisteredPendingEmail->value);

        $this->assertDatabaseHas('users', ['email' => 'juan@test.cl']);
        $this->assertDatabaseHas('profiles', ['first_name' => 'Juan', 'last_name' => 'Pérez']);
        Event::assertDispatched(\App\Events\UserRegistered::class);
    }

    public function test_registro_rechaza_email_duplicado(): void
    {
        User::factory()->create(['email' => 'existente@test.cl']);

        $response = $this->postJson('/api/register', [
            'first_name' => 'Otro',
            'last_name' => 'Usuario',
            'email' => 'existente@test.cl',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertStatus(422);
    }

    public function test_verificacion_email_actualiza_estado_a_pending_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'verify@test.cl',
            'verification_status' => VerificationStatus::RegisteredPendingEmail,
        ]);
        $svc = app(EmailVerificationService::class);
        $token = $svc->createVerification($user);

        $response = $this->getJson('/api/email/verify?token=' . $token);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertEquals(VerificationStatus::EmailVerifiedPendingOtp, $user->verification_status);
    }

    public function test_otp_verificado_actualiza_a_fully_verified_y_devuelve_token(): void
    {
        $user = User::factory()->create([
            'email' => 'otp@test.cl',
            'verification_status' => VerificationStatus::EmailVerifiedPendingOtp,
        ]);
        $otp = app(OtpService::class);
        $code = $otp->generateForUser($user);

        $response = $this->postJson('/api/otp/verify', [
            'email' => 'otp@test.cl',
            'code' => $code,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
        $user->refresh();
        $this->assertEquals(VerificationStatus::FullyVerified, $user->verification_status);
    }
}
