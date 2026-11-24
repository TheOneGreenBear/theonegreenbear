<?php

namespace App\Console\Commands;

use App\Actions\Sync\SiteSync;
use App\Models\SyncRun;
use Illuminate\Console\Command;

class SyncSite extends Command
{
    protected $signature = 'site:sync {provider? : Only sync one provider (steam, mal, spotify, github)}';

    protected $description = 'Pull live data (games, anime, music, projects) from external services into the database';

    public function handle(SiteSync $sync): int
    {
        $only = $this->argument('provider');

        if ($only && ! in_array($only, SiteSync::providers(), true)) {
            $this->error("Unknown provider [{$only}]. Available: ".implode(', ', SiteSync::providers()));

            return self::INVALID;
        }

        $failed = false;

        // Each provider runs on its own; a failure doesn't stop the others.
        foreach ($only ? [$only] : SiteSync::providers() as $name) {
            $run = $sync->run($name);

            match ($run->status) {
                SyncRun::SUCCEEDED => $this->info("{$name}: synced {$run->items} items"),
                SyncRun::SKIPPED => $this->warn("{$name}: skipped, not configured"),
                SyncRun::FAILED => $this->error("{$name}: sync failed ({$run->error})"),
            };

            $failed = $failed || $run->failed();
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
