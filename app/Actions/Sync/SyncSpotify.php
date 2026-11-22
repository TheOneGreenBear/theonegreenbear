<?php

namespace App\Actions\Sync;

use App\Models\ProviderSnapshot;
use App\Services\Spotify\SpotifyClient;

class SyncSpotify
{
    public function __construct(private readonly SpotifyClient $client) {}

    /**
     * Store a snapshot of what the owner is listening to.
     *
     * @return int|null Number of items stored, or null when Spotify isn't configured or connected.
     */
    public function __invoke(): ?int
    {
        if (! $this->client->isConfigured() || ! $this->client->isConnected()) {
            return null;
        }

        // Fetch everything first, so a failed request leaves the old snapshot intact.
        $current = $this->client->currentlyPlaying();

        $payload = [
            'now_playing' => ($current['item'] ?? null) && ($current['item']['type'] ?? 'track') === 'track'
                ? [
                    'is_playing' => (bool) ($current['is_playing'] ?? false),
                    'track' => $this->track($current['item']),
                ]
                : null,
            'recent' => array_map(fn ($item) => [
                ...$this->track($item['track']),
                'played_at' => $item['played_at'] ?? null,
            ], $this->client->recentlyPlayed()),
            'top_tracks' => array_map($this->track(...), $this->client->topTracks()),
            'top_artists' => array_map(fn ($artist) => [
                'id' => $artist['id'],
                'name' => $artist['name'],
                'image' => $this->image($artist['images'] ?? []),
                'url' => $artist['external_urls']['spotify'] ?? null,
                'genres' => array_slice($artist['genres'] ?? [], 0, 2),
            ], $this->client->topArtists()),
            'playlists' => array_map(fn ($playlist) => [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'image' => $this->image($playlist['images'] ?? []),
                'url' => $playlist['external_urls']['spotify'] ?? null,
                'tracks' => $playlist['tracks']['total'] ?? null,
            ], $this->client->ownPublicPlaylists()),
        ];

        ProviderSnapshot::store(SpotifyClient::PROVIDER, $payload);

        return count($payload['recent']) + count($payload['top_tracks']) + count($payload['top_artists']) + count($payload['playlists']);
    }

    private function track(array $track): array
    {
        return [
            'id' => $track['id'] ?? null,
            'name' => $track['name'] ?? 'Unknown track',
            'artists' => implode(', ', array_column($track['artists'] ?? [], 'name')),
            'album' => $track['album']['name'] ?? null,
            'image' => $this->image($track['album']['images'] ?? []),
            'url' => $track['external_urls']['spotify'] ?? null,
        ];
    }

    /** Spotify lists images largest first; pick a mid-size one (~300px) when available. */
    private function image(array $images): ?string
    {
        return ($images[1] ?? $images[0] ?? [])['url'] ?? null;
    }
}
