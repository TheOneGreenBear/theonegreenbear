<?php

namespace App\Http\Controllers;

use App\Models\SteamGame;
use Illuminate\View\View;

class GamesController extends Controller
{
    public function __invoke(): View
    {
        $mostPlayed = SteamGame::where('playtime_forever', '>', 0)
            ->orderByDesc('playtime_forever')
            ->limit(10)
            ->get();

        return view('games', [
            'recent' => SteamGame::recentlyPlayed()->get(),
            'mostPlayed' => $mostPlayed,
            'maxMinutes' => max(1, (int) $mostPlayed->max('playtime_forever')),
            'gameCount' => SteamGame::count(),
            'totalMinutes' => (int) SteamGame::sum('playtime_forever'),
            'lastSynced' => SteamGame::max('updated_at'),
        ]);
    }
}
