<?php

namespace App\Providers;

use App\Models\User;
use App\Services\GitHub\GitHubClient;
use App\Services\MyAnimeList\MyAnimeListClient;
use App\Services\Spotify\SpotifyClient;
use App\Services\Steam\SteamClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Resolved per use so config changes (e.g. in tests) are picked up.
        $this->app->bind(SteamClient::class, fn () => SteamClient::fromConfig());
        $this->app->bind(MyAnimeListClient::class, fn () => MyAnimeListClient::fromConfig());
        $this->app->bind(SpotifyClient::class, fn () => SpotifyClient::fromConfig());
        $this->app->bind(GitHubClient::class, fn () => GitHubClient::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The site owner (SITE_OWNER_EMAIL) can use owner-only actions such as connecting Spotify.
        Gate::define('site-owner', function (User $user): bool {
            $owner = config('site.owner_email');

            return filled($owner) && strcasecmp($user->email, $owner) === 0;
        });
    }
}
