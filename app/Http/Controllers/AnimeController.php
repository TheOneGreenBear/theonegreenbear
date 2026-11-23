<?php

namespace App\Http\Controllers;

use App\Models\AnimeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $all = AnimeEntry::all();

        // Every status is rendered up front so the tabs can switch instantly in the browser.
        $groups = collect(AnimeEntry::STATUSES)->map(function (string $label, string $status) use ($all) {
            $entries = $all->where('status', $status);

            // Completed shows my favourites first; other tabs show what I touched most recently.
            return ($status === 'completed'
                ? $entries->sortBy([['score', 'desc'], ['title', 'asc']])
                : $entries->sortByDesc('list_updated_at')
            )->values();
        });

        $counts = $groups->map->count();

        $status = $request->query('status');
        if (! is_string($status) || ! array_key_exists($status, AnimeEntry::STATUSES)) {
            $status = $counts['watching'] > 0 ? 'watching' : 'completed';
        }

        return view('anime', [
            'status' => $status,
            'groups' => $groups,
            'counts' => $counts,
            'total' => $all->count(),
            'episodesWatched' => (int) $all->sum('episodes_watched'),
            'averageScore' => $all->where('score', '>', 0)->avg('score'),
            'lastSynced' => $all->max('updated_at'),
        ]);
    }
}
