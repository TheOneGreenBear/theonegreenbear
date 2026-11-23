<?php

namespace App\Services\GitHub;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Reads a user's public repositories from the GitHub REST API.
 *
 * @see https://docs.github.com/en/rest/repos/repos#list-repositories-for-a-user
 */
class GitHubClient
{
    private const BASE_URL = 'https://api.github.com';

    /** Safety cap on pages followed (100 repositories each). */
    private const MAX_PAGES = 10;

    public function __construct(
        private readonly ?string $username = null,
        private readonly ?string $token = null,
    ) {}

    public static function fromConfig(): self
    {
        return new self(config('services.github.username'), config('services.github.token'));
    }

    public function isConfigured(): bool
    {
        return filled($this->username);
    }

    /**
     * Public repositories the user owns (forks included; callers decide what to show).
     *
     * @return list<array<string, mixed>>
     */
    public function publicRepositories(): array
    {
        $repos = [];

        for ($page = 1; $page <= self::MAX_PAGES; $page++) {
            $batch = Http::baseUrl(self::BASE_URL)
                ->timeout(10)
                ->retry(2, 500, throw: false)
                ->withHeaders([
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                    // GitHub rejects requests without a User-Agent.
                    'User-Agent' => config('app.name', 'Laravel').'-site',
                ])
                ->when($this->token, fn ($request) => $request->withToken($this->token))
                ->get('/users/'.rawurlencode($this->username).'/repos', [
                    'type' => 'owner',
                    'sort' => 'pushed',
                    'per_page' => 100,
                    'page' => $page,
                ])
                ->throw()
                ->json();

            array_push($repos, ...$batch);

            if (count($batch) < 100) {
                return $repos;
            }
        }

        throw new RuntimeException('GitHub account has more repositories than the page limit allows.');
    }
}
