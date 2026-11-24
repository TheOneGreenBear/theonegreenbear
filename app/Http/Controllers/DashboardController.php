<?php

namespace App\Http\Controllers;

use App\Actions\Sync\SiteSync;
use App\Models\SyncRun;
use App\Services\GitHub\GitHubClient;
use App\Services\MyAnimeList\MyAnimeListClient;
use App\Services\Spotify\SpotifyClient;
use App\Services\Steam\SteamClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Provider name => [label, client used to check its config]. */
    private const PROVIDERS = [
        'steam' => ['Steam', SteamClient::class],
        'mal' => ['MyAnimeList', MyAnimeListClient::class],
        'spotify' => ['Spotify', SpotifyClient::class],
        'github' => ['GitHub', GitHubClient::class],
    ];

    public function show(Request $request): View|RedirectResponse
    {
        // Fortify sends everyone here after login, but only the owner has anything to manage.
        if ($request->user()->cannot('site-owner')) {
            return to_route('home');
        }

        $providers = collect(SiteSync::providers())->map(fn (string $name) => [
            'name' => $name,
            'label' => self::PROVIDERS[$name][0],
            'configured' => app(self::PROVIDERS[$name][1])->isConfigured(),
            'lastRun' => SyncRun::latestFor($name),
            'lastSuccess' => SyncRun::latestFor($name, SyncRun::SUCCEEDED),
        ]);

        return view('dashboard', [
            'providers' => $providers,
            'spotifyConnected' => app(SpotifyClient::class)->isConnected(),
        ]);
    }

    /** Run one provider's sync straight away. */
    public function sync(string $provider, SiteSync $sync): RedirectResponse
    {
        $run = $sync->run($provider);
        $label = self::PROVIDERS[$provider][0];

        return to_route('dashboard')->with(...match ($run->status) {
            SyncRun::SUCCEEDED => ['sync_status', __(':provider synced :count :items.', [
                'provider' => $label,
                'count' => number_format($run->items),
                'items' => Str::plural('item', $run->items),
            ])],
            SyncRun::SKIPPED => ['sync_error', __(':provider was skipped because it isn\'t set up yet.', ['provider' => $label])],
            SyncRun::FAILED => ['sync_error', __(':provider sync failed: :error', ['provider' => $label, 'error' => $run->error])],
        });
    }
}
