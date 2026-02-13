<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $table = 'user_sessions';

    protected $fillable = [
        'user_id',
        'token_hash',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'last_activity_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDeviceAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (stripos($ua, 'Mac') !== false) {
            return 'Mac';
        }
        if (stripos($ua, 'Windows') !== false) {
            return 'Windows';
        }
        if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) {
            return 'iPhone/iPad';
        }
        if (stripos($ua, 'Android') !== false) {
            return 'Android';
        }
        if (stripos($ua, 'Linux') !== false) {
            return 'Linux';
        }

        return 'Desconocido';
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (preg_match('/Chrome\/(\d+)/i', $ua, $m) && stripos($ua, 'Edg') === false) {
            return 'Chrome '.($m[1] ?? '');
        }
        if (preg_match('/Edg\/(\d+)/i', $ua, $m)) {
            return 'Edge '.($m[1] ?? '');
        }
        if (preg_match('/Firefox\/(\d+)/i', $ua, $m)) {
            return 'Firefox '.($m[1] ?? '');
        }
        if (preg_match('/Safari\/(\d+)/i', $ua, $m) && stripos($ua, 'Chrome') === false) {
            return 'Safari '.($m[1] ?? '');
        }

        return 'Navegador';
    }
}
