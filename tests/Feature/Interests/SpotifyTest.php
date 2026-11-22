<?php

use App\Models\ProviderSnapshot;
use App\Models\ServiceToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.spotify.client_id' => 'test-client-id',
        'services.spotify.client_secret' => 'test-client-secret',
        'services.spotify.redirect' => 'https://example.com/spotify/callback',
        'site.owner_email' => 'owner@example.com',
    ]);
});

function spotifyTrack(string $id, string $name, string $artist = 'Test Artist'): array
{
    return [
        'id' => $id,
        'type' => 'track',
        'name' => $name,
        'artists' => [['name' => $artist]],
        'album' => ['name' => 'Test Album', 'images' => [['url' => 'https://example.com/640.jpg'], ['url' => 'https://example.com/300.jpg']]],
        'external_urls' => ['spotify' => "https://open.spotify.com/track/{$id}"],
    ];
}

function fakeSpotifyApi(?array $playing = null): void
{
    Http::fake([
        'accounts.spotify.com/api/token' => Http::response([
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
            'scope' => 'user-top-read',
        ]),
        'api.spotify.com/v1/me/player/currently-playing*' => $playing
            ? Http::response(['is_playing' => true, 'item' => $playing])
            : Http::response(null, 204),
        'api.spotify.com/v1/me/player/recently-played*' => Http::response(['items' => [
            ['track' => spotifyTrack('r1', 'Recent Song'), 'played_at' => '2026-01-02T03:04:05Z'],
        ]]),
        'api.spotify.com/v1/me/top/tracks*' => Http::response(['items' => [spotifyTrack('t1', 'Top Song')]]),
        'api.spotify.com/v1/me/top/artists*' => Http::response(['items' => [
            ['id' => 'a1', 'name' => 'Top Artist', 'images' => [], 'genres' => ['rock'], 'external_urls' => ['spotify' => 'https://open.spotify.com/artist/a1']],
        ]]),
        'api.spotify.com/v1/me/playlists*' => Http::response(['items' => [
            ['id' => 'p1', 'name' => 'My Mix', 'public' => true, 'owner' => ['id' => 'example_user'], 'images' => [], 'tracks' => ['total' => 12], 'external_urls' => ['spotify' => 'https://open.spotify.com/playlist/p1']],
            ['id' => 'p2', 'name' => 'Private Mix', 'public' => false, 'owner' => ['id' => 'example_user'], 'images' => [], 'tracks' => ['total' => 3], 'external_urls' => []],
            ['id' => 'p3', 'name' => 'Followed Mix', 'public' => true, 'owner' => ['id' => 'someone_else'], 'images' => [], 'tracks' => ['total' => 9], 'external_urls' => []],
        ]]),
        'api.spotify.com/v1/me' => Http::response(['id' => 'example_user']),
    ]);
}

function connectSpotify(array $overrides = []): ServiceToken
{
    return ServiceToken::create([
        'provider' => 'spotify',
        'access_token' => 'old-access-token',
        'refresh_token' => 'old-refresh-token',
        'expires_at' => now()->addHour(),
        ...$overrides,
    ]);
}

// --- Sync ---------------------------------------------------------------------------------------

test('sync is skipped until spotify is connected', function () {
    Http::fake();

    $this->artisan('site:sync spotify')->expectsOutputToContain('skipped')->assertSuccessful();

    Http::assertNothingSent();
});

test('sync stores a snapshot of listening data', function () {
    connectSpotify();
    fakeSpotifyApi(playing: spotifyTrack('n1', 'Playing Song'));

    $this->artisan('site:sync spotify')->assertSuccessful();

    $data = ProviderSnapshot::for('spotify')->payload;

    expect($data['now_playing']['track']['name'])->toBe('Playing Song')
        ->and($data['now_playing']['track']['image'])->toBe('https://example.com/300.jpg')
        ->and($data['recent'][0]['name'])->toBe('Recent Song')
        ->and($data['top_tracks'][0]['name'])->toBe('Top Song')
        ->and($data['top_artists'][0]['name'])->toBe('Top Artist')
        ->and(array_column($data['playlists'], 'name'))->toBe(['My Mix']);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.spotify.com')
        && $request->hasHeader('Authorization', 'Bearer old-access-token'));
    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'accounts.spotify.com'));
});

test('sync records nothing playing when spotify returns no content', function () {
    connectSpotify();
    fakeSpotifyApi(playing: null);

    $this->artisan('site:sync spotify')->assertSuccessful();

    expect(ProviderSnapshot::for('spotify')->payload['now_playing'])->toBeNull();
});

test('an expiring access token is refreshed before calling the api', function () {
    connectSpotify(['expires_at' => now()->addSeconds(10)]);
    fakeSpotifyApi();

    $this->artisan('site:sync spotify')->assertSuccessful();

    $token = ServiceToken::for('spotify');
    expect($token->access_token)->toBe('new-access-token')
        ->and($token->refresh_token)->toBe('new-refresh-token')
        ->and($token->expires_at->isAfter(now()->addMinutes(50)))->toBeTrue();

    Http::assertSent(fn ($request) => $request->url() === 'https://accounts.spotify.com/api/token'
        && $request['grant_type'] === 'refresh_token'
        && $request['refresh_token'] === 'old-refresh-token');
});

test('tokens are stored encrypted', function () {
    connectSpotify();

    $raw = DB::table('service_tokens')->where('provider', 'spotify')->first();

    expect($raw->access_token)->not->toContain('old-access-token')
        ->and($raw->refresh_token)->not->toContain('old-refresh-token');
});

test('a failed sync keeps the previous snapshot', function () {
    connectSpotify();
    ProviderSnapshot::store('spotify', ['recent' => [['name' => 'Kept Song']]]);
    Http::fake(['api.spotify.com/*' => Http::response('Server error', 500)]);

    $this->artisan('site:sync spotify')->assertFailed();

    expect(ProviderSnapshot::for('spotify')->payload['recent'][0]['name'])->toBe('Kept Song');
});

// --- Connecting -----------------------------------------------------------------------------------

test('only the site owner can connect spotify', function () {
    $this->get(route('spotify.connect'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create(['email' => 'visitor@example.com']))
        ->get(route('spotify.connect'))
        ->assertForbidden();
});

test('the owner is sent to spotify with a state value', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);

    $response = $this->actingAs($owner)->get(route('spotify.connect'));

    $response->assertRedirectContains('https://accounts.spotify.com/authorize?');
    $response->assertRedirectContains('client_id=test-client-id');
    $response->assertRedirectContains(rawurlencode('playlist-read-private'));
    $response->assertRedirectContains('state='.session('spotify_oauth_state'));
});

test('the callback stores tokens and runs a first sync', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);
    fakeSpotifyApi();

    $this->actingAs($owner)
        ->withSession(['spotify_oauth_state' => 'expected-state'])
        ->get(route('spotify.callback', ['code' => 'auth-code', 'state' => 'expected-state']))
        ->assertRedirect(route('spotify'))
        ->assertSessionHas('spotify_status');

    expect(ServiceToken::for('spotify')->refresh_token)->toBe('new-refresh-token')
        ->and(ProviderSnapshot::for('spotify'))->not->toBeNull();

    Http::assertSent(fn ($request) => $request->url() === 'https://accounts.spotify.com/api/token'
        && $request['grant_type'] === 'authorization_code'
        && $request['code'] === 'auth-code'
        && $request['redirect_uri'] === 'https://example.com/spotify/callback');
});

test('the callback rejects a mismatched state', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);
    Http::fake();

    $this->actingAs($owner)
        ->withSession(['spotify_oauth_state' => 'expected-state'])
        ->get(route('spotify.callback', ['code' => 'auth-code', 'state' => 'forged-state']))
        ->assertRedirect(route('spotify'))
        ->assertSessionHas('spotify_error');

    expect(ServiceToken::for('spotify'))->toBeNull();
    Http::assertNothingSent();
});

test('the owner can disconnect spotify', function () {
    connectSpotify();
    $owner = User::factory()->create(['email' => 'owner@example.com']);

    $this->actingAs($owner)->post(route('spotify.disconnect'))->assertRedirect(route('spotify'));

    expect(ServiceToken::for('spotify'))->toBeNull();
});

// --- Pages ----------------------------------------------------------------------------------------

test('spotify page shows an empty state before the first sync', function () {
    $this->get(route('spotify'))
        ->assertOk()
        ->assertSee('Spotify isn&#039;t connected yet.', false)
        ->assertDontSee('Connect Spotify');
});

test('only the owner sees the connect button', function () {
    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']))
        ->get(route('spotify'))
        ->assertSee('Connect Spotify');

    $this->actingAs(User::factory()->create(['email' => 'visitor@example.com']))
        ->get(route('spotify'))
        ->assertDontSee('Connect Spotify');
});

test('spotify page shows now playing when the snapshot is fresh', function () {
    ProviderSnapshot::store('spotify', [
        'now_playing' => ['is_playing' => true, 'track' => ['name' => 'Playing Song', 'artists' => 'Test Artist', 'album' => null, 'image' => null, 'url' => '#']],
        'recent' => [['name' => 'Recent Song', 'artists' => 'Test Artist', 'album' => null, 'image' => null, 'url' => '#', 'played_at' => null]],
        'top_tracks' => [['name' => 'Top Song', 'artists' => 'Test Artist', 'album' => null, 'image' => null, 'url' => '#']],
        'top_artists' => [],
        'playlists' => [],
    ]);

    $this->get(route('spotify'))
        ->assertOk()
        ->assertSeeInOrder(['Now playing', 'Playing Song', 'Top tracks this month', 'Top Song']);

    $this->get(route('home'))->assertSee('Now playing: Playing Song');
});

test('a stale snapshot shows the last played track instead of now playing', function () {
    ProviderSnapshot::store('spotify', [
        'now_playing' => ['is_playing' => true, 'track' => ['name' => 'Old Song', 'artists' => 'Test Artist', 'album' => null, 'image' => null, 'url' => '#']],
        'recent' => [['name' => 'Recent Song', 'artists' => 'Test Artist', 'album' => null, 'image' => null, 'url' => '#', 'played_at' => null]],
    ]);
    $this->travel(30)->minutes();

    $this->get(route('home'))
        ->assertSee('Last played: Recent Song')
        ->assertDontSee('Now playing');
});
