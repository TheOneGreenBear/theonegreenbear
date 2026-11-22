<?php

use App\Models\AnimeEntry;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.myanimelist.client_id' => 'test-client-id',
        'services.myanimelist.username' => 'example_user',
    ]);
});

function malEntry(int $id, string $title, string $status, array $overrides = []): array
{
    return [
        'node' => [
            'id' => $id,
            'title' => $title,
            'main_picture' => ['medium' => "https://example.com/{$id}.jpg", 'large' => "https://example.com/{$id}l.jpg"],
            'num_episodes' => 12,
            'mean' => 8.1,
            'media_type' => 'tv',
        ],
        'list_status' => [
            'status' => $status,
            'score' => 0,
            'num_episodes_watched' => 0,
            'is_rewatching' => false,
            'updated_at' => '2026-01-02T03:04:05+00:00',
            ...$overrides,
        ],
    ];
}

test('sync stores every entry across pages', function () {
    Http::fake([
        'api.myanimelist.net/v2/users/example_user/animelist*' => Http::sequence()
            ->push([
                'data' => [malEntry(1, 'Test Saga', 'completed', ['score' => 9, 'num_episodes_watched' => 12])],
                'paging' => ['next' => 'https://api.myanimelist.net/v2/users/example_user/animelist?offset=1000&limit=1000'],
            ])
            ->push([
                'data' => [malEntry(2, 'Example Show', 'watching', ['num_episodes_watched' => 5])],
                'paging' => [],
            ]),
    ]);

    $this->artisan('site:sync mal')->assertSuccessful();

    expect(AnimeEntry::count())->toBe(2);

    expect(AnimeEntry::firstWhere('mal_id', 1))
        ->title->toBe('Test Saga')
        ->status->toBe('completed')
        ->score->toBe(9)
        ->image_url->toBe('https://example.com/1l.jpg');

    expect(AnimeEntry::firstWhere('mal_id', 2))
        ->episodes_watched->toBe(5)
        ->progressPercent()->toBe(41.7);

    Http::assertSent(fn ($request) => $request->hasHeader('X-MAL-CLIENT-ID', 'test-client-id'));
    Http::assertSent(fn ($request) => str_contains($request->url(), 'offset=1000'));
    Http::assertSentCount(2);
});

test('sync removes entries no longer on the list', function () {
    AnimeEntry::create(['mal_id' => 99, 'title' => 'Removed Show', 'status' => 'dropped']);

    Http::fake(['api.myanimelist.net/*' => Http::response([
        'data' => [malEntry(1, 'Test Saga', 'completed')],
        'paging' => [],
    ])]);

    $this->artisan('site:sync mal')->assertSuccessful();

    expect(AnimeEntry::pluck('mal_id')->all())->toBe([1]);
});

test('sync is skipped when MyAnimeList is not configured', function () {
    config(['services.myanimelist.client_id' => null]);
    Http::fake();

    $this->artisan('site:sync mal')
        ->expectsOutputToContain('skipped')
        ->assertSuccessful();

    Http::assertNothingSent();
});

test('a failed request keeps the existing list', function () {
    AnimeEntry::create(['mal_id' => 1, 'title' => 'Test Saga', 'status' => 'watching']);

    Http::fake(['api.myanimelist.net/*' => Http::response('Server error', 500)]);

    $this->artisan('site:sync mal')->assertFailed();

    expect(AnimeEntry::count())->toBe(1);
});
