<?php

namespace App\Http\Controllers;

use App\Models\AnimeEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnimeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $counts = AnimeEntry::toBase()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $status = $request->query('status');
        if (! is_string($status) || ! array_key_exists($status, AnimeEntry::STATUSES)) {
            $status = ($counts['watching'] ?? 0) > 0 ? 'watching' : 'completed';
        }

        $entries = AnimeEntry::where('status', $status)
            // Completed shows my favourites first; other tabs show what I touched most recently.
            ->when($status === 'completed',
                fn ($query) => $query->orderByDesc('score')->orderBy('title'),
                fn ($query) => $query->orderByDesc('list_updated_at'),
            )
            ->get();

        return view('anime', [
            'status' => $status,
            'entries' => $entries,
            'counts' => $counts,
            'total' => $counts->sum(),
            'episodesWatched' => (int) AnimeEntry::sum('episodes_watched'),
            'averageScore' => AnimeEntry::where('score', '>', 0)->avg('score'),
            'lastSynced' => AnimeEntry::max('updated_at'),
        ]);
    }
}
