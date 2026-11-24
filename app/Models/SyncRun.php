<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class SyncRun extends Model
{
    use Prunable;

    public const SUCCEEDED = 'succeeded';

    /** The provider isn't configured (or, for Spotify, not connected), so nothing was fetched. */
    public const SKIPPED = 'skipped';

    public const FAILED = 'failed';

    /** How long run history is kept; `model:prune` removes anything older. */
    public const KEEP_DAYS = 14;

    protected $fillable = [
        'provider',
        'status',
        'items',
        'error',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'integer',
            'duration_ms' => 'integer',
        ];
    }

    /** The most recent run for a provider, optionally limited to one status. */
    public static function latestFor(string $provider, ?string $status = null): ?self
    {
        return static::where('provider', $provider)
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->latest('id')
            ->first();
    }

    public function succeeded(): bool
    {
        return $this->status === self::SUCCEEDED;
    }

    public function failed(): bool
    {
        return $this->status === self::FAILED;
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(self::KEEP_DAYS));
    }
}
