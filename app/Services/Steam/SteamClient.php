<?php

namespace App\Services\Steam;

use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the Steam Web API endpoints the site needs.
 *
 * @see https://developer.valvesoftware.com/wiki/Steam_Web_API
 */
class SteamClient
{
    private const BASE_URL = 'https://api.steampowered.com/IPlayerService';

    public function __construct(
        private readonly ?string $key = null,
        private readonly ?string $steamId = null,
    ) {}

    public static function fromConfig(): self
    {
        return new self(config('services.steam.key'), config('services.steam.steam_id'));
    }

    public function isConfigured(): bool
    {
        return filled($this->key) && filled($this->steamId);
    }

    /**
     * Every game on the profile, with lifetime playtime.
     *
     * @return list<array{appid: int, name?: string, playtime_forever?: int, rtime_last_played?: int}>
     */
    public function ownedGames(): array
    {
        return $this->get('GetOwnedGames/v1/', [
            'include_appinfo' => 1,
            'include_played_free_games' => 1,
        ]);
    }

    /**
     * Games played in the last two weeks.
     *
     * @return list<array{appid: int, name?: string, playtime_2weeks?: int, playtime_forever?: int}>
     */
    public function recentlyPlayedGames(): array
    {
        return $this->get('GetRecentlyPlayedGames/v1/');
    }

    private function get(string $endpoint, array $query = []): array
    {
        return Http::timeout(10)
            ->retry(2, 500, throw: false)
            ->get(self::BASE_URL.'/'.$endpoint, [
                'key' => $this->key,
                'steamid' => $this->steamId,
                'format' => 'json',
                ...$query,
            ])
            ->throw()
            ->json('response.games', []);
    }
}
