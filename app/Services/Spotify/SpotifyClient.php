<?php

namespace App\Services\Spotify;

use App\Models\ServiceToken;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Spotify Web API access on the site owner's behalf (authorization code flow).
 *
 * @see https://developer.spotify.com/documentation/web-api
 */
class SpotifyClient
{
    public const PROVIDER = 'spotify';

    public const SCOPES = [
        'user-read-currently-playing',
        'user-read-recently-played',
        'user-top-read',
        // Required by GET /me/playlists, even though only public playlists are shown.
        'playlist-read-private',
    ];

    private const ACCOUNTS_URL = 'https://accounts.spotify.com';

    private const API_URL = 'https://api.spotify.com/v1';

    public function __construct(
        private readonly ?string $clientId = null,
        private readonly ?string $clientSecret = null,
        private readonly ?string $redirectUri = null,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            config('services.spotify.client_id'),
            config('services.spotify.client_secret'),
            config('services.spotify.redirect') ?: route('spotify.callback'),
        );
    }

    /** App credentials are present. */
    public function isConfigured(): bool
    {
        return filled($this->clientId) && filled($this->clientSecret);
    }

    /** The owner has connected their account. */
    public function isConnected(): bool
    {
        return ServiceToken::for(self::PROVIDER) !== null;
    }

    public function authorizeUrl(string $state): string
    {
        return self::ACCOUNTS_URL.'/authorize?'.http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', self::SCOPES),
            'state' => $state,
        ]);
    }

    /** Swap the code from the callback for tokens and store them. */
    public function connect(string $code): ServiceToken
    {
        $data = $this->tokenRequest([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
        ]);

        return ServiceToken::updateOrCreate(['provider' => self::PROVIDER], [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => now()->addSeconds($data['expires_in']),
            'scopes' => $data['scope'] ?? null,
        ]);
    }

    public function disconnect(): void
    {
        ServiceToken::where('provider', self::PROVIDER)->delete();
    }

    /** @return array<string, mixed>|null Null when nothing is playing. */
    public function currentlyPlaying(): ?array
    {
        $response = $this->api()->get('/me/player/currently-playing', ['additional_types' => 'track']);

        // 204 No Content means nothing is playing.
        return $response->status() === 204 ? null : $response->throw()->json();
    }

    public function recentlyPlayed(int $limit = 20): array
    {
        return $this->api()->get('/me/player/recently-played', ['limit' => $limit])->throw()->json('items', []);
    }

    public function topTracks(int $limit = 10, string $range = 'short_term'): array
    {
        return $this->api()->get('/me/top/tracks', ['limit' => $limit, 'time_range' => $range])->throw()->json('items', []);
    }

    public function topArtists(int $limit = 10, string $range = 'short_term'): array
    {
        return $this->api()->get('/me/top/artists', ['limit' => $limit, 'time_range' => $range])->throw()->json('items', []);
    }

    /** Public playlists the owner created (not ones they only follow). */
    public function ownPublicPlaylists(): array
    {
        $me = $this->api()->get('/me')->throw()->json('id');
        $items = $this->api()->get('/me/playlists', ['limit' => 50])->throw()->json('items', []);

        return array_values(array_filter(
            $items,
            fn ($playlist) => $playlist && ($playlist['public'] ?? false) && ($playlist['owner']['id'] ?? null) === $me,
        ));
    }

    private function api(): PendingRequest
    {
        return Http::baseUrl(self::API_URL)
            ->timeout(10)
            ->withToken($this->accessToken());
    }

    /** A valid access token, refreshed first if it is about to expire. */
    private function accessToken(): string
    {
        $token = ServiceToken::for(self::PROVIDER)
            ?? throw new RuntimeException('Spotify is not connected.');

        if ($token->isExpiring()) {
            $data = $this->tokenRequest([
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
            ]);

            $token->update([
                'access_token' => $data['access_token'],
                // Spotify may rotate the refresh token; keep the old one if it doesn't.
                'refresh_token' => $data['refresh_token'] ?? $token->refresh_token,
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]);
        }

        return $token->access_token;
    }

    private function tokenRequest(array $form): array
    {
        /** @var Response $response */
        $response = Http::asForm()
            ->timeout(10)
            ->withBasicAuth((string) $this->clientId, (string) $this->clientSecret)
            ->post(self::ACCOUNTS_URL.'/api/token', $form);

        return $response->throw()->json();
    }
}
