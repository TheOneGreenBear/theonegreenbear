<?php

use App\Models\SyncRun;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.steam.key' => 'test-key',
        'services.steam.steam_id' => '76561190000000000',
    ]);
});

test('a successful sync records the run with its item count', function () {
    Http::fake([
        'api.steampowered.com/IPlayerService/GetOwnedGames/*' => Http::response(['response' => ['games' => [
            ['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600],
            ['appid' => 20, 'name' => 'Example Racer', 'playtime_forever' => 90],
        ]]]),
        'api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/*' => Http::response(['response' => ['games' => []]]),
    ]);

    $this->artisan('site:sync steam')->assertSuccessful();

    $run = SyncRun::sole();
    expect($run)
        ->provider->toBe('steam')
        ->status->toBe(SyncRun::SUCCEEDED)
        ->items->toBe(2)
        ->error->toBeNull();
});

test('a skipped sync is recorded as skipped', function () {
    config(['services.steam.key' => null]);

    $this->artisan('site:sync steam')->assertSuccessful();

    expect(SyncRun::sole())
        ->status->toBe(SyncRun::SKIPPED)
        ->items->toBeNull();
});

test('a failed sync records the error', function () {
    Http::fake(['api.steampowered.com/*' => Http::response('Server error', 500)]);

    $this->artisan('site:sync steam')
        ->expectsOutputToContain('steam: sync failed')
        ->assertFailed();

    $run = SyncRun::sole();
    expect($run->status)->toBe(SyncRun::FAILED)
        ->and($run->error)->toContain('500');
});

test('syncing everything records one run per provider', function () {
    config([
        'services.steam.key' => null,
        'services.myanimelist.client_id' => null,
        'services.spotify.client_id' => null,
        'services.github.username' => null,
    ]);

    $this->artisan('site:sync')->assertSuccessful();

    expect(SyncRun::pluck('provider')->sort()->values()->all())
        ->toBe(['github', 'mal', 'spotify', 'steam']);
});

test('latestFor finds the newest run, optionally by status', function () {
    SyncRun::create(['provider' => 'steam', 'status' => SyncRun::SUCCEEDED, 'items' => 5, 'duration_ms' => 10]);
    SyncRun::create(['provider' => 'steam', 'status' => SyncRun::FAILED, 'error' => 'Boom', 'duration_ms' => 10]);
    SyncRun::create(['provider' => 'mal', 'status' => SyncRun::SUCCEEDED, 'items' => 9, 'duration_ms' => 10]);

    expect(SyncRun::latestFor('steam')->status)->toBe(SyncRun::FAILED)
        ->and(SyncRun::latestFor('steam', SyncRun::SUCCEEDED)->items)->toBe(5)
        ->and(SyncRun::latestFor('github'))->toBeNull();
});

test('runs older than the retention window are pruned', function () {
    $old = SyncRun::create(['provider' => 'steam', 'status' => SyncRun::SUCCEEDED, 'items' => 1, 'duration_ms' => 10]);
    $old->forceFill(['created_at' => now()->subDays(SyncRun::KEEP_DAYS + 1)])->save();
    $recent = SyncRun::create(['provider' => 'steam', 'status' => SyncRun::SUCCEEDED, 'items' => 2, 'duration_ms' => 10]);

    $this->artisan('model:prune', ['--model' => [SyncRun::class]])->assertSuccessful();

    expect(SyncRun::pluck('id')->all())->toBe([$recent->id]);
});
