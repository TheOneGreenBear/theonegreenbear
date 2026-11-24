<?php

namespace App\Actions\Sync;

use App\Models\SyncRun;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

/** Runs one provider's sync and records the outcome as a SyncRun. */
class SiteSync
{
    /** Provider name => invokable action returning a count, or null when not configured. */
    public const PROVIDERS = [
        'steam' => SyncSteamGames::class,
        'mal' => SyncAnimeList::class,
        'spotify' => SyncSpotify::class,
        'github' => SyncGitHubRepos::class,
    ];

    /** @return list<string> */
    public static function providers(): array
    {
        return array_keys(self::PROVIDERS);
    }

    public function run(string $provider): SyncRun
    {
        $action = self::PROVIDERS[$provider] ?? throw new InvalidArgumentException("Unknown sync provider [{$provider}].");

        $started = hrtime(true);
        $items = null;
        $error = null;

        try {
            $items = app($action)();
            $status = $items === null ? SyncRun::SKIPPED : SyncRun::SUCCEEDED;
        } catch (Throwable $e) {
            // The old data stays in place; keep the reason so it shows on the dashboard.
            report($e);
            $status = SyncRun::FAILED;
            $error = Str::limit($e->getMessage(), 1000);
        }

        return SyncRun::create([
            'provider' => $provider,
            'status' => $status,
            'items' => $items,
            'error' => $error,
            'duration_ms' => intdiv(hrtime(true) - $started, 1_000_000),
        ]);
    }
}
