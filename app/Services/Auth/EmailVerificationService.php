<?php

namespace App\Services\Auth;

use App\Enums\VerificationStatus;
use App\Models\Auth\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmailVerificationService
{
    public const LIFETIME_MINUTES = 60;

    public function createVerification(User $user): string
    {
        $token = Str::random(64);
        EmailVerification::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token_hash' => Hash::make($token),
            'expires_at' => now()->addMinutes(self::LIFETIME_MINUTES),
        ]);

        return $token;
    }

    public function verifyByToken(string $token): ?User
    {
        $verifications = EmailVerification::whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->get();

        foreach ($verifications as $verification) {
            if (Hash::check($token, $verification->token_hash)) {
                $verification->update(['verified_at' => now()]);
                $user = $verification->user;
                $user->update([
                    'email_verified_at' => now(),
                    'verification_status' => VerificationStatus::EmailVerifiedPendingOtp,
                ]);

                return $user;
            }
        }

        return null;
    }

    public function isExpired(EmailVerification $verification): bool
    {
        return $verification->expires_at->isPast();
    }
}
