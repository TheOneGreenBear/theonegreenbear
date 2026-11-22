<?php

use App\Models\SteamGame;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.steam.key' => 'test-key',
        'services.steam.steam_id' => '76561190000000000',
    ]);
});

function fakeSteam(array $owned, array $recent = []): void
{
    Http::fake([
        'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => ['game_count' => count($owned), 'games' => $owned]]),
        'api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/*' => Http::response(['response' => ['total_count' => count($recent), 'games' => $recent]]),
    ]);
}

test('sync stores owned and recently played games', function () {
    fakeSteam(
        owned: [
            ['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600, 'rtime_last_played' => 1_700_000_000],
            ['appid' => 20, 'name' => 'Example Racer', 'playtime_forever' => 90, 'rtime_last_played' => 0],
        ],
        recent: [
            ['appid' => 10, 'name' => 'Test Quest', 'playtime_2weeks' => 120, 'playtime_forever' => 600],
        ],
    );

    $this->artisan('site:sync steam')->assertSuccessful();

    expect(SteamGame::count())->toBe(2);

    $quest = SteamGame::firstWhere('appid', 10);
    expect($quest->name)->toBe('Test Quest')
        ->and($quest->playtime_forever)->toBe(600)
        ->and($quest->playtime_2weeks)->toBe(120)
        ->and($quest->last_played_at->timestamp)->toBe(1_700_000_000);

    $racer = SteamGame::firstWhere('appid', 20);
    expect($racer->playtime_2weeks)->toBeNull()
        ->and($racer->last_played_at)->toBeNull();

    Http::assertSent(fn ($request) => $request['key'] === 'test-key'
        && $request['steamid'] === '76561190000000000');
});

test('sync clears recent playtime for games no longer played recently', function () {
    SteamGame::create(['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600, 'playtime_2weeks' => 120]);

    fakeSteam(owned: [['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 650]]);

    $this->artisan('site:sync steam')->assertSuccessful();

    expect(SteamGame::firstWhere('appid', 10))
        ->playtime_2weeks->toBeNull()
        ->playtime_forever->toBe(650);
});

test('sync is skipped when steam is not configured', function () {
    config(['services.steam.key' => null]);
    Http::fake();

    $this->artisan('site:sync steam')
        ->expectsOutputToContain('skipped')
        ->assertSuccessful();

    Http::assertNothingSent();
});

test('a failed request keeps the existing games', function () {
    SteamGame::create(['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600, 'playtime_2weeks' => 120]);

    Http::fake(['api.steampowered.com/*' => Http::response('Server error', 500)]);

    $this->artisan('site:sync steam')->assertFailed();

    expect(SteamGame::firstWhere('appid', 10)->playtime_2weeks)->toBe(120);
});

test('unknown providers are rejected', function () {
    $this->artisan('site:sync nope')->assertExitCode(2);
});
