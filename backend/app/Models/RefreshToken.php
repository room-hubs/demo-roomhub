<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    protected $fillable = ['user_id', 'token', 'device_name', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public static function generate($user, string $device): array
    {
        $plainToken = Str::random(64);

        $refreshToken = self::create([
            'user_id'     => $user->id,
            'token'       => hash('sha256', $plainToken),
            'device_name' => $device,
            'expires_at'  => now()->addDays(30),
        ]);

        return ['model' => $refreshToken, 'plain' => $plainToken];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
