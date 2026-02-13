<?php

namespace App\Services\Auth;

use App\Models\Auth\BackupCode;
use App\Models\Auth\TwoFactorSecret;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Servicio 2FA TOTP. El secreto debe ser Base32 para que Google Authenticator y similares generen los 6 dígitos.
 * Para verificación TOTP en producción: composer require pragmarx/google2fa
 */
class TwoFactorService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(User $user): array
    {
        $secret = $this->generateBase32Secret(32);

        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret_encrypted' => Crypt::encryptString($secret),
                'confirmed_at' => null,
            ]
        );

        $issuer = rawurlencode(config('app.name'));
        $label = $issuer . ':' . rawurlencode($user->email);
        $qrUrl = 'otpauth://totp/' . $label . '?secret=' . $secret . '&issuer=' . $issuer . '&algorithm=SHA1&digits=6&period=30';

        return [
            'secret' => $secret,
            'qr_url' => $qrUrl,
        ];
    }

    /**
     * Genera un secreto en Base32 válido para TOTP (RFC 6238). Las apps (Google Authenticator, etc.) requieren Base32.
     */
    private function generateBase32Secret(int $length): string
    {
        $alphabet = self::BASE32_ALPHABET;
        $result = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[ord($bytes[$i]) % 32];
        }
        return $result;
    }

    public function confirm(User $user, string $code): bool
    {
        $record = $user->twoFactorSecret;
        if (! $record || $record->confirmed_at) {
            return false;
        }

        $secret = Crypt::decryptString($record->secret_encrypted);
        if (! $this->verifyTotpCode($secret, $code)) {
            return false;
        }

        $record->update(['confirmed_at' => now()]);

        return true;
    }

    public function verify(User $user, string $code): bool
    {
        if ($this->verifyBackupCode($user, $code)) {
            return true;
        }

        $record = $user->twoFactorSecret;
        if (! $record?->isConfirmed()) {
            return false;
        }

        $secret = Crypt::decryptString($record->secret_encrypted);

        return $this->verifyTotpCode($secret, $code);
    }

    /**
     * Verificación TOTP (RFC 6238). Comprueba el código en la ventana actual y la anterior/siguiente (tolerancia).
     */
    private function verifyTotpCode(string $secret, string $code): bool
    {
        if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
            return (new \PragmaRX\Google2FA\Google2FA)->verifyKey($secret, $code);
        }
        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return false;
        }
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            if ($this->getTotpCode($secret, $timeSlice + $i) === $code) {
                return true;
            }
        }
        return false;
    }

    /**
     * Genera el código TOTP de 6 dígitos para un time slice (RFC 6238).
     */
    private function getTotpCode(string $base32Secret, int $timeSlice): string
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
        $alphabet = self::BASE32_ALPHABET;
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

    public function verifyBackupCode(User $user, string $code): bool
    {
        $hash = hash('sha256', strtoupper($code));
        $backup = BackupCode::where('user_id', $user->id)
            ->whereNull('used_at')
            ->get()
            ->first(fn (BackupCode $b) => hash_equals($b->code_hash, $hash));

        if (! $backup) {
            return false;
        }
        $backup->update(['used_at' => now()]);

        return true;
    }

    public function generateBackupCodes(User $user, int $count = 8): array
    {
        BackupCode::where('user_id', $user->id)->delete();

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(Str::random(8));
            $codes[] = $code;
            BackupCode::create([
                'user_id' => $user->id,
                'code_hash' => hash('sha256', $code),
            ]);
        }

        return $codes;
    }

    public function disable(User $user): void
    {
        $user->twoFactorSecret?->delete();
        BackupCode::where('user_id', $user->id)->delete();
    }
}
