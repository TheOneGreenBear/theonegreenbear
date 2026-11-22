<?php

namespace App\Http\Controllers;

use App\Actions\Sync\SyncSpotify;
use App\Models\ProviderSnapshot;
use App\Services\Spotify\Listening;
use App\Services\Spotify\SpotifyClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class SpotifyController extends Controller
{
    private const STATE_KEY = 'spotify_oauth_state';

    public function show(Request $request, SpotifyClient $client): View
    {
        $snapshot = ProviderSnapshot::for(SpotifyClient::PROVIDER);
        $isOwner = (bool) $request->user()?->can('site-owner');

        return view('spotify', [
            'listening' => Listening::current($snapshot),
            'data' => $snapshot?->payload ?? [],
            'lastSynced' => $snapshot?->updated_at,
            'isOwner' => $isOwner,
            'configured' => $isOwner && $client->isConfigured(),
            'connected' => $isOwner && $client->isConnected(),
        ]);
    }

    /** Send the owner to Spotify to grant access. */
    public function connect(Request $request, SpotifyClient $client): RedirectResponse
    {
        if (! $client->isConfigured()) {
            return to_route('spotify')->with('spotify_error', __('Add SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET to .env first.'));
        }

        $state = Str::random(40);
        $request->session()->put(self::STATE_KEY, $state);

        return redirect()->away($client->authorizeUrl($state));
    }

    /** Spotify redirects back here after the owner approves (or denies) access. */
    public function callback(Request $request, SpotifyClient $client, SyncSpotify $sync): RedirectResponse
    {
        $expected = $request->session()->pull(self::STATE_KEY);

        if (! $expected || ! hash_equals($expected, (string) $request->query('state'))) {
            return to_route('spotify')->with('spotify_error', __('Spotify sign-in expired or was tampered with. Please try again.'));
        }

        if ($request->filled('error') || ! $request->filled('code')) {
            return to_route('spotify')->with('spotify_error', __('Spotify access was not granted.'));
        }

        $client->connect($request->query('code'));

        try {
            $sync();
        } catch (Throwable $e) {
            // Connected fine; the scheduled sync will retry.
            report($e);
        }

        return to_route('spotify')->with('spotify_status', __('Spotify connected.'));
    }

    public function disconnect(SpotifyClient $client): RedirectResponse
    {
        $client->disconnect();

        return to_route('spotify')->with('spotify_status', __('Spotify disconnected. Existing data stays until you reconnect.'));
    }
}
