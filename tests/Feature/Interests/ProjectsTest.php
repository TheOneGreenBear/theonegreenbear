<?php

use App\Models\GithubRepo;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.github.username' => 'example-user',
        'services.github.token' => null,
    ]);
});

function githubRepo(int $id, string $name, array $overrides = []): array
{
    return [
        'id' => $id,
        'name' => $name,
        'full_name' => "example-user/{$name}",
        'owner' => ['login' => 'example-user'],
        'private' => false,
        'fork' => false,
        'archived' => false,
        'description' => "About {$name}",
        'html_url' => "https://github.com/example-user/{$name}",
        'homepage' => '',
        'language' => 'PHP',
        'stargazers_count' => 0,
        'forks_count' => 0,
        'topics' => [],
        'pushed_at' => '2026-01-02T03:04:05Z',
        'created_at' => '2025-01-02T03:04:05Z',
        ...$overrides,
    ];
}

function storeRepo(int $id, string $name, array $overrides = []): GithubRepo
{
    return GithubRepo::create([
        'github_id' => $id,
        'name' => $name,
        'full_name' => "example-user/{$name}",
        'html_url' => "https://github.com/example-user/{$name}",
        'language' => 'PHP',
        'topics' => [],
        'pushed_at' => now(),
        ...$overrides,
    ]);
}

// --- Sync ---------------------------------------------------------------------------------------

test('sync stores own public repositories and skips forks', function () {
    Http::fake(['api.github.com/users/example-user/repos*' => Http::response([
        githubRepo(1, 'test-site', ['homepage' => 'https://example.com', 'stargazers_count' => 3, 'topics' => ['laravel', 'featured']]),
        githubRepo(2, 'forked-tool', ['fork' => true]),
        githubRepo(3, 'old-thing', ['archived' => true, 'language' => null, 'description' => null]),
    ])]);

    $this->artisan('site:sync github')->assertSuccessful();

    expect(GithubRepo::orderBy('github_id')->pluck('name')->all())->toBe(['test-site', 'old-thing']);

    $site = GithubRepo::firstWhere('github_id', 1);
    expect($site->homepage)->toBe('https://example.com')
        ->and($site->stars)->toBe(3)
        ->and($site->isFeatured())->toBeTrue()
        ->and($site->displayTopics())->toBe(['laravel']);

    $old = GithubRepo::firstWhere('github_id', 3);
    expect($old->is_archived)->toBeTrue()
        ->and($old->homepage)->toBeNull();

    Http::assertSent(fn ($request) => $request->hasHeader('User-Agent') && $request['type'] === 'owner');
});

test('sync removes repositories that are gone from GitHub', function () {
    storeRepo(99, 'deleted-repo');
    Http::fake(['api.github.com/*' => Http::response([githubRepo(1, 'test-site')])]);

    $this->artisan('site:sync github')->assertSuccessful();

    expect(GithubRepo::pluck('github_id')->all())->toBe([1]);
});

test('sync follows pages and sends the token when set', function () {
    config(['services.github.token' => 'test-token']);
    $firstPage = array_map(fn ($i) => githubRepo($i, "repo-{$i}"), range(1, 100));

    Http::fake(['api.github.com/*' => Http::sequence()
        ->push($firstPage)
        ->push([githubRepo(101, 'repo-101')]),
    ]);

    $this->artisan('site:sync github')->assertSuccessful();

    expect(GithubRepo::count())->toBe(101);
    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request['page'] === 2 && $request->hasHeader('Authorization', 'Bearer test-token'));
});

test('sync is skipped when no username is set', function () {
    config(['services.github.username' => null]);
    Http::fake();

    $this->artisan('site:sync github')->expectsOutputToContain('skipped')->assertSuccessful();

    Http::assertNothingSent();
});

test('a failed request keeps the existing projects', function () {
    storeRepo(1, 'test-site');
    Http::fake(['api.github.com/*' => Http::response(['message' => 'API rate limit exceeded'], 403)]);

    $this->artisan('site:sync github')->assertFailed();

    expect(GithubRepo::count())->toBe(1);
});

// --- Page -----------------------------------------------------------------------------------------

test('projects page shows an empty state before the first sync', function () {
    $this->get(route('projects'))
        ->assertOk()
        ->assertSee('GitHub isn&#039;t connected yet.', false);
});

test('projects page lists featured projects first', function () {
    storeRepo(1, 'PlainTool', ['pushed_at' => now()]);
    storeRepo(2, 'StarProject', ['topics' => ['featured'], 'homepage' => 'https://example.com', 'pushed_at' => now()->subYear()]);

    $this->get(route('projects'))
        ->assertOk()
        ->assertSeeInOrder(['Featured', 'Star Project', 'Live site', 'More projects', 'Plain Tool']);
});

test('projects page filters by language', function () {
    storeRepo(1, 'php-app', ['language' => 'PHP']);
    storeRepo(2, 'java-app', ['language' => 'Java']);

    // Every filter is rendered for instant switching; the URL decides which panel is visible.
    $response = $this->get(route('projects', ['language' => 'Java']))->assertOk();
    expect(visibleTabPanels($response->getContent()))->toBe(['Java']);

    // Unknown languages fall back to showing everything.
    $response = $this->get(route('projects', ['language' => 'Cobol']))->assertOk();
    expect(visibleTabPanels($response->getContent()))->toBe(['']);
});

test('each language panel lists only that language', function () {
    storeRepo(1, 'php-app', ['language' => 'PHP']);
    storeRepo(2, 'java-app', ['language' => 'Java']);

    $html = $this->get(route('projects'))->getContent();
    preg_match('/data-tab-panel="Java".*?(?=data-tab-panel=|$)/s', $html, $javaPanel);

    expect($javaPanel[0])->toContain('java app')->not->toContain('php app');
});

test('welcome page tile shows the latest project', function () {
    storeRepo(1, 'OlderThing', ['pushed_at' => now()->subMonth()]);
    storeRepo(2, 'NewerThing', ['pushed_at' => now()]);

    $this->get(route('home'))->assertSee('Latest: Newer Thing');
});

test('repository names are made readable', function (string $name, string $expected) {
    expect((new GithubRepo(['name' => $name]))->displayName())->toBe($expected);
})->with([
    ['SteamApplication-', 'Steam Application'],
    ['E-Commerce-Website', 'E Commerce Website'],
    ['DataAlgorithms', 'Data Algorithms'],
    ['random-tv', 'random tv'],
]);
