<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Models\Auth\BackupCode;
use App\Models\Auth\EmailVerification;
use App\Models\Auth\OtpCode;
use App\Models\Auth\TwoFactorSecret;
use App\Models\Auth\UserSession;
use App\Models\Billing\Subscription;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_status',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_status' => VerificationStatus::class,
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function emailVerifications(): HasMany
    {
        return $this->hasMany(EmailVerification::class);
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    public function twoFactorSecret(): HasOne
    {
        return $this->hasOne(TwoFactorSecret::class);
    }

    public function backupCodes(): HasMany
    {
        return $this->hasMany(BackupCode::class);
    }

    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function isFullyVerified(): bool
    {
        return $this->verification_status === VerificationStatus::FullyVerified;
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->twoFactorSecret?->isConfirmed() ?? false;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
