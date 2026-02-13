<?php

namespace Tests\Unit;

use App\Models\Auth\TwoFactorSecret;
use App\Models\User;
use App\Services\Auth\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class TwoFactorServiceTest extends TestCase
{
    use RefreshDatabase;

    private TwoFactorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TwoFactorService;
    }

    public function test_confirm_rechaza_codigo_incorrecto(): void
    {
        $user = User::factory()->create();
        $secret = 'JBSWY3DPEHPK3PXP';
        TwoFactorSecret::create([
            'user_id' => $user->id,
            'secret_encrypted' => Crypt::encryptString($secret),
            'confirmed_at' => null,
        ]);

        $this->assertFalse($this->service->confirm($user, '000000'));
        $this->assertFalse($this->service->confirm($user, '123456'));
        $this->assertFalse($this->service->confirm($user, '12')); // menos de 6
        $this->assertFalse($this->service->confirm($user, 'abcdef'));
    }

    public function test_confirm_acepta_codigo_totp_valido(): void
    {
        $user = User::factory()->create();
        // Secret conocido; fijamos el tiempo para poder calcular el código esperado
        $secret = 'JBSWY3DPEHPK3PXP';
        Date::setTestNow(now()->startOfMinute());

        TwoFactorSecret::create([
            'user_id' => $user->id,
            'secret_encrypted' => Crypt::encryptString($secret),
            'confirmed_at' => null,
        ]);

        $code = $this->computeTotp($secret, (int) floor(now()->timestamp / 30));
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code, 'TOTP debe ser 6 dígitos');

        $result = $this->service->confirm($user, $code);
        $this->assertTrue($result, 'confirm() debe aceptar el código TOTP correcto');

        $user->twoFactorSecret->refresh();
        $this->assertNotNull($user->twoFactorSecret->confirmed_at);
    }

    public function test_confirm_rechaza_si_ya_esta_confirmado(): void
    {
        $user = User::factory()->create();
        $secret = 'JBSWY3DPEHPK3PXP';
        TwoFactorSecret::create([
            'user_id' => $user->id,
            'secret_encrypted' => Crypt::encryptString($secret),
            'confirmed_at' => now(),
        ]);

        $code = $this->computeTotp($secret, (int) floor(time() / 30));
        $this->assertFalse($this->service->confirm($user, $code));
    }

    public function test_generate_secret_devuelve_secreto_base32_y_url_qr(): void
    {
        $user = User::factory()->create();
        $data = $this->service->generateSecret($user);

        $this->assertArrayHasKey('secret', $data);
        $this->assertArrayHasKey('qr_url', $data);
        $this->assertMatchesRegularExpression('/^[A-Z2-7]{32}$/', $data['secret'], 'El secreto debe ser Base32 de 32 caracteres');
        $this->assertStringContainsString('otpauth://', $data['qr_url']);
        $this->assertStringContainsString('Buscame', $data['qr_url']);
        $this->assertStringContainsString(urlencode($user->email), $data['qr_url']);

        $this->assertDatabaseHas('two_factor_secrets', ['user_id' => $user->id]);
    }

    /**
     * Calcula código TOTP (RFC 6238) para un time slice. Misma lógica que TwoFactorService para comprobar compatibilidad.
     */
    private function computeTotp(string $base32Secret, int $timeSlice): string
    {
        $secret = $this->base32Decode($base32Secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated = (
            ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF)
        );
        return str_pad((string) ($truncated % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $buffer = 0;
        $bufferSize = 0;
        $output = '';
        for ($i = 0; $i < strlen($input); $i++) {
            $pos = strpos($alphabet, $input[$i]);
            if ($pos === false) {
                continue;
            }
            $buffer = ($buffer << 5) | $pos;
            $bufferSize += 5;
            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $output .= chr(($buffer >> $bufferSize) & 0xFF);
            }
        }
        return $output;
    }
}
