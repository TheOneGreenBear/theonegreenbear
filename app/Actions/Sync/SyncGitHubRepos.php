<?php

namespace App\Actions\Sync;

use App\Models\GithubRepo;
use App\Services\GitHub\GitHubClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncGitHubRepos
{
    public function __construct(private readonly GitHubClient $client) {}

    /**
     * Mirror the account's own public, non-fork repositories into github_repos.
     *
     * @return int|null Number of repositories stored, or null when GitHub isn't configured.
     */
    public function __invoke(): ?int
    {
        if (! $this->client->isConfigured()) {
            return null;
        }

        // Fetch first, so a failed request leaves the old data intact.
        $rows = collect($this->client->publicRepositories())
            ->reject(fn (array $repo) => ($repo['fork'] ?? false) || ($repo['private'] ?? false))
            ->map(fn (array $repo) => [
                'github_id' => $repo['id'],
                'name' => $repo['name'],
                'full_name' => $repo['full_name'],
                'description' => $repo['description'] ?? null,
                'html_url' => $repo['html_url'],
                'homepage' => filled($repo['homepage'] ?? null) ? $repo['homepage'] : null,
                'language' => $repo['language'] ?? null,
                'stars' => $repo['stargazers_count'] ?? 0,
                'forks' => $repo['forks_count'] ?? 0,
                'topics' => json_encode($repo['topics'] ?? []),
                'is_archived' => $repo['archived'] ?? false,
                'pushed_at' => isset($repo['pushed_at']) ? Carbon::parse($repo['pushed_at']) : null,
                'repo_created_at' => isset($repo['created_at']) ? Carbon::parse($repo['created_at']) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values();

        DB::transaction(function () use ($rows) {
            // Repositories that were deleted, made private or turned out to be forks disappear here too.
            GithubRepo::whereNotIn('github_id', $rows->pluck('github_id'))->delete();

            GithubRepo::upsert(
                $rows->all(),
                uniqueBy: ['github_id'],
                update: ['name', 'full_name', 'description', 'html_url', 'homepage', 'language', 'stars', 'forks', 'topics', 'is_archived', 'pushed_at', 'repo_created_at', 'updated_at'],
            );
        });

        return $rows->count();
    }
}
