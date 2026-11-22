<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\SpotifyController;
use App\Models\AnimeEntry;
use App\Models\ProviderSnapshot;
use App\Models\SteamGame;
use App\Services\Spotify\Listening;
use App\Services\Spotify\SpotifyClient;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome', [
        'lastPlayed' => SteamGame::whereNotNull('last_played_at')->latest('last_played_at')->first(),
        'nowWatching' => AnimeEntry::where('status', 'watching')->latest('list_updated_at')->first(),
        'listening' => Listening::current(ProviderSnapshot::for(SpotifyClient::PROVIDER)),
    ]);
})->name('home');

Route::get('games', GamesController::class)->name('games');
Route::get('anime', AnimeController::class)->name('anime');
Route::get('spotify', [SpotifyController::class, 'show'])->name('spotify');

Route::middleware(['auth', 'can:site-owner'])->prefix('spotify')->name('spotify.')->group(function () {
    Route::get('connect', [SpotifyController::class, 'connect'])->name('connect');
    Route::get('callback', [SpotifyController::class, 'callback'])->name('callback');
    Route::post('disconnect', [SpotifyController::class, 'disconnect'])->name('disconnect');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
