<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderSnapshot extends Model
{
    protected $fillable = [
        'provider',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public static function for(string $provider): ?self
    {
        return static::firstWhere('provider', $provider);
    }

    public static function store(string $provider, array $payload): self
    {
        $snapshot = static::firstOrNew(['provider' => $provider]);
        $snapshot->payload = $payload;
        // Touch even when the payload is unchanged, so "updated" reflects the last successful sync.
        $snapshot->updated_at = now();
        $snapshot->save();

        return $snapshot;
    }
}
