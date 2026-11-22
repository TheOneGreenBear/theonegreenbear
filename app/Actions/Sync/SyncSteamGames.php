<?php

namespace App\Actions\Sync;

use App\Models\SteamGame;
use App\Services\Steam\SteamClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncSteamGames
{
    public function __construct(private readonly SteamClient $client) {}

    /**
     * Pull the profile's games into the steam_games table.
     *
     * @return int|null Number of games stored, or null when Steam isn't configured.
     */
    public function __invoke(): ?int
    {
        if (! $this->client->isConfigured()) {
            return null;
        }

        // Fetch everything before touching the database, so a failed request leaves the old data intact.
        $owned = collect($this->client->ownedGames())->keyBy('appid');
        $recent = collect($this->client->recentlyPlayedGames())->keyBy('appid');

        $rows = $owned->union($recent)->map(function (array $game, int $appid) use ($owned, $recent) {
            $ownedGame = $owned->get($appid, []);
            $lastPlayed = $ownedGame['rtime_last_played'] ?? 0;

            return [
                'appid' => $appid,
                'name' => $game['name'] ?? "App {$appid}",
                'playtime_forever' => $ownedGame['playtime_forever'] ?? $game['playtime_forever'] ?? 0,
                'playtime_2weeks' => $recent->get($appid, [])['playtime_2weeks'] ?? null,
                'last_played_at' => $lastPlayed > 0 ? Carbon::createFromTimestamp($lastPlayed) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->values();

        DB::transaction(function () use ($rows) {
            // Recent playtime only counts while Steam still reports the game as recent.
            SteamGame::query()->update(['playtime_2weeks' => null]);

            foreach ($rows->chunk(200) as $chunk) {
                SteamGame::upsert(
                    $chunk->all(),
                    uniqueBy: ['appid'],
                    update: ['name', 'playtime_forever', 'playtime_2weeks', 'last_played_at', 'updated_at'],
                );
            }
        });

        return $rows->count();
    }
}
