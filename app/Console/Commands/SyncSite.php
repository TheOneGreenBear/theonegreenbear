<?php

namespace App\Console\Commands;

use App\Actions\Sync\SyncSteamGames;
use Illuminate\Console\Command;
use Throwable;

class SyncSite extends Command
{
    protected $signature = 'site:sync {provider? : Only sync one provider (steam)}';

    protected $description = 'Pull live data (games, anime, music) from external services into the database';

    /** Provider name => invokable action returning a count, or null when not configured. */
    private const PROVIDERS = [
        'steam' => SyncSteamGames::class,
    ];

    public function handle(): int
    {
        $only = $this->argument('provider');

        if ($only && ! array_key_exists($only, self::PROVIDERS)) {
            $this->error("Unknown provider [{$only}]. Available: ".implode(', ', array_keys(self::PROVIDERS)));

            return self::INVALID;
        }

        $providers = $only ? [$only => self::PROVIDERS[$only]] : self::PROVIDERS;
        $failed = false;

        foreach ($providers as $name => $action) {
            try {
                $count = app($action)();
            } catch (Throwable $e) {
                // Keep going with the other providers; the old data stays in place.
                report($e);
                $this->error("{$name}: sync failed ({$e->getMessage()})");
                $failed = true;

                continue;
            }

            $count === null
                ? $this->warn("{$name}: skipped, not configured")
                : $this->info("{$name}: synced {$count} items");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
