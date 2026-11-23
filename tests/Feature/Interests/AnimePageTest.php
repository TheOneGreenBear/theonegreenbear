<?php

use App\Models\AnimeEntry;

test('anime page shows an empty state before the first sync', function () {
    $this->get(route('anime'))
        ->assertOk()
        ->assertSee('MyAnimeList isn&#039;t connected yet.', false);
});

test('anime page defaults to the watching tab', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Test Saga', 'status' => 'watching', 'num_episodes' => 12, 'episodes_watched' => 3]);
    AnimeEntry::create(['mal_id' => 2, 'title' => 'Example Show', 'status' => 'completed']);

    $response = $this->get(route('anime'))
        ->assertOk()
        ->assertSee('Test Saga')
        ->assertSee('Ep 3 / 12');

    // Every status is rendered for instant tab switching, but only the watching panel is visible.
    expect(visibleTabPanels($response->getContent()))->toBe(['watching']);
});

test('anime page falls back to completed when nothing is being watched', function () {
    AnimeEntry::create(['mal_id' => 2, 'title' => 'Example Show', 'status' => 'completed']);

    $response = $this->get(route('anime'))->assertSee('Example Show');

    expect(visibleTabPanels($response->getContent()))->toBe(['completed']);
});

test('the status in the url picks the visible tab', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Test Saga', 'status' => 'watching']);
    AnimeEntry::create(['mal_id' => 2, 'title' => 'Example Show', 'status' => 'dropped']);

    $response = $this->get(route('anime', ['status' => 'dropped']))->assertOk();

    expect(visibleTabPanels($response->getContent()))->toBe(['dropped']);
});

test('completed tab lists highest scored first', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Okay Show', 'status' => 'completed', 'score' => 6]);
    AnimeEntry::create(['mal_id' => 2, 'title' => 'Great Show', 'status' => 'completed', 'score' => 10]);

    $this->get(route('anime', ['status' => 'completed']))
        ->assertSeeInOrder(['Great Show', 'Okay Show']);
});

test('an unknown status falls back to the default tab', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Test Saga', 'status' => 'watching']);

    $response = $this->get(route('anime', ['status' => 'nope']))
        ->assertOk()
        ->assertSee('Test Saga');

    expect(visibleTabPanels($response->getContent()))->toBe(['watching']);
});

test('welcome page tile shows what is being watched', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Test Saga', 'status' => 'watching', 'list_updated_at' => now()]);

    $this->get(route('home'))->assertSee('Watching: Test Saga');
});
