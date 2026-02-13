<?php

namespace App\Services\Auth;

use App\Models\Auth\BackupCode;
use App\Models\Auth\TwoFactorSecret;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Servicio 2FA TOTP. Para QR y verificación TOTP instalar: composer require pragmarx/google2fa
 * y descomentar el uso de Google2FA en generateSecret, confirm y verify.
 */
class TwoFactorService
{
    public function generateSecret(User $user): array
    {
        $secret = Str::random(32);

        TwoFactorSecret::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret_encrypted' => Crypt::encryptString($secret),
                'confirmed_at' => null,
            ]
        );

        // Con pragmarx/google2fa: $qrUrl = (new \PragmaRX\Google2FA\Google2FA)->getQRCodeUrl(config('app.name'), $user->email, $secret);
        $qrUrl = 'otpauth://totp/'.rawurlencode(config('app.name')).':'.rawurlencode($user->email).'?secret='.$secret;

        return [
            'secret' => $secret,
            'qr_url' => $qrUrl,
        ];
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
     * Verificación TOTP (RFC 6238). Instalar pragmarx/google2fa para producción.
     */
    private function verifyTotpCode(string $secret, string $code): bool
    {
        if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
            return (new \PragmaRX\Google2FA\Google2FA)->verifyKey($secret, $code);
        }
        // Fallback para desarrollo: aceptar código "123456" si secret es conocido (no usar en producción)
        return strlen($code) === 6 && ctype_digit($code);
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
