<?php

namespace App\Services\Spotify;

use App\Models\ProviderSnapshot;

/** Works out what to show as "now playing" from the latest Spotify snapshot. */
class Listening
{
    /** A snapshot older than this can't honestly claim something is playing right now. */
    private const LIVE_FOR_MINUTES = 10;

    /**
     * @return array{label: string, playing: bool, track: array<string, mixed>}|null
     */
    public static function current(?ProviderSnapshot $snapshot): ?array
    {
        if (! $snapshot) {
            return null;
        }

        $nowPlaying = $snapshot->payload['now_playing'] ?? null;
        $isLive = $snapshot->updated_at?->isAfter(now()->subMinutes(self::LIVE_FOR_MINUTES));

        if ($nowPlaying && $nowPlaying['is_playing'] && $isLive) {
            return ['label' => 'Now playing', 'playing' => true, 'track' => $nowPlaying['track']];
        }

        $last = $snapshot->payload['recent'][0] ?? null;

        return $last ? ['label' => 'Last played', 'playing' => false, 'track' => $last] : null;
    }
}
