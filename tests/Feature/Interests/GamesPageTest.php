<?php

use App\Models\SteamGame;

test('games page shows an empty state before the first sync', function () {
    $this->get(route('games'))
        ->assertOk()
        ->assertSee('Steam isn&#039;t connected yet.', false);
});

test('games page lists recent and most played games', function () {
    SteamGame::create(['appid' => 10, 'name' => 'Test Quest', 'playtime_forever' => 600, 'playtime_2weeks' => 120]);
    SteamGame::create(['appid' => 20, 'name' => 'Example Racer', 'playtime_forever' => 6000]);

    $this->get(route('games'))
        ->assertOk()
        ->assertSeeInOrder(['Last two weeks', 'Test Quest', 'Most played', 'Example Racer', 'Test Quest'])
        ->assertSee('2.0 h last 2 weeks');
});

test('welcome page tile shows the last played game', function () {
    SteamGame::create(['appid' => 10, 'name' => 'Test Quest', 'last_played_at' => now()->subDay()]);
    SteamGame::create(['appid' => 20, 'name' => 'Example Racer', 'last_played_at' => now()->subWeek()]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Last played: Test Quest');
});
