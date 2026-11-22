<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceToken extends Model
{
    protected $fillable = [
        'provider',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'expires_at' => 'datetime',
        ];
    }

    public static function for(string $provider): ?self
    {
        return static::firstWhere('provider', $provider);
    }

    /** True when the access token is expired or about to be. */
    public function isExpiring(): bool
    {
        return $this->expires_at->isBefore(now()->addMinute());
    }
}
