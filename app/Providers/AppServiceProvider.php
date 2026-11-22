<?php

namespace App\Providers;

use App\Services\MyAnimeList\MyAnimeListClient;
use App\Services\Steam\SteamClient;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
