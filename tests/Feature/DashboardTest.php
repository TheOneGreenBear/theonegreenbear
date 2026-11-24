<?php

use App\Models\ServiceToken;
use App\Models\SyncRun;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'site.owner_email' => 'owner@example.com',
        'services.steam.key' => 'test-key',
        'services.steam.steam_id' => '76561190000000000',
        'services.myanimelist.client_id' => null,
        'services.spotify.client_id' => 'test-client-id',
        'services.spotify.client_secret' => 'test-client-secret',
        'services.spotify.redirect' => 'https://example.com/spotify/callback',
        'services.github.username' => null,
    ]);
});

function siteOwner(): User
{
    return User::factory()->create(['email' => 'owner@example.com']);
}

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('other users are sent to the home page', function () {
    $this->actingAs(User::factory()->create(['email' => 'visitor@example.com']))
        ->get(route('dashboard'))
        ->assertRedirect(route('home'));
});

test('the owner sees a card for each provider', function () {
    $this->actingAs(siteOwner())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(['data-provider="steam"', 'data-provider="mal"', 'data-provider="spotify"', 'data-provider="github"'], escape: false)
        ->assertSee(['Never synced', 'Not set up', 'Not connected', 'Connect Spotify']);
});

test('the owner sees the last run status, item count and error', function () {
    SyncRun::create(['provider' => 'steam', 'status' => SyncRun::SUCCEEDED, 'items' => 1234, 'duration_ms' => 10]);
    SyncRun::create(['provider' => 'steam', 'status' => SyncRun::FAILED, 'error' => 'HTTP request returned status code 500', 'duration_ms' => 10]);

    $this->actingAs(siteOwner())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(['Failed', '1,234', 'HTTP request returned status code 500']);
});

test('a connected spotify account is not asked to connect again', function () {
    ServiceToken::create([
        'provider' => 'spotify',
        'access_token' => 'test-access-token',
        'refresh_token' => 'test-refresh-token',
        'expires_at' => now()->addHour(),
    ]);

    $this->actingAs(siteOwner())
        ->get(route('dashboard'))
        ->assertDontSee('Not connected')
        ->assertDontSee('Connect Spotify');
});

test('the owner can run a sync straight away', function () {
    Http::fake([
        'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => ['games' => [
            ['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600],
        ]]]),
        'api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/*' => Http::response(['response' => ['games' => []]]),
    ]);

    $this->actingAs(siteOwner())
        ->post(route('dashboard.sync', 'steam'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('sync_status', 'Steam synced 1 item.');

    expect(SyncRun::sole())
        ->provider->toBe('steam')
        ->status->toBe(SyncRun::SUCCEEDED);
});

test('a failed sync from the dashboard shows the error', function () {
    Http::fake(['api.steampowered.com/*' => Http::response('Server error', 500)]);

    $this->actingAs(siteOwner())
        ->post(route('dashboard.sync', 'steam'))
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('sync_error', fn (string $message) => str_starts_with($message, 'Steam sync failed:'));

    expect(SyncRun::sole()->status)->toBe(SyncRun::FAILED);
});

test('only the owner can run a sync', function () {
    $this->actingAs(User::factory()->create(['email' => 'visitor@example.com']))
        ->post(route('dashboard.sync', 'steam'))
        ->assertForbidden();

    expect(SyncRun::count())->toBe(0);
});

test('unknown providers are not routed', function () {
    $this->actingAs(siteOwner())
        ->post('/dashboard/sync/nope')
        ->assertNotFound();
});
