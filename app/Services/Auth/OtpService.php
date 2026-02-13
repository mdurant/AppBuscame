<?php

namespace App\Services\Auth;

use App\Models\Auth\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpService
{
    public const PURPOSE_POST_EMAIL_VERIFY = 'post_email_verify';

    public const LIFETIME_MINUTES = 15;

    public function generateForUser(User $user, string $purpose = self::PURPOSE_POST_EMAIL_VERIFY): string
    {
        $this->invalidatePreviousForUser($user, $purpose);

        $code = $this->generateNumericCode(6);
        OtpCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::LIFETIME_MINUTES),
        ]);

        return $code;
    }

    public function verify(User $user, string $code, string $purpose = self::PURPOSE_POST_EMAIL_VERIFY): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp || ! Hash::check($code, $otp->code_hash)) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }

    public function invalidatePreviousForUser(User $user, string $purpose): void
    {
        OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }

    private function generateNumericCode(int $length): string
    {
        $max = (int) str_repeat('9', $length);

        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }
}
