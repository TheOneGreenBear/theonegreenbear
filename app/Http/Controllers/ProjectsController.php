<?php

namespace App\Http\Controllers;

use App\Models\GithubRepo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $repos = GithubRepo::orderByDesc('pushed_at')->get();

        // Language => number of repositories, most used first.
        $languages = $repos->whereNotNull('language')->countBy('language')->sortDesc();

        $language = $request->query('language');
        if (! is_string($language) || ! $languages->has($language)) {
            $language = null;
        }

        $shown = $language ? $repos->where('language', $language) : $repos;

        return view('projects', [
            'featured' => $language ? collect() : $repos->filter->isFeatured()->values(),
            'projects' => $language ? $shown->values() : $shown->reject->isFeatured()->values(),
            'languages' => $languages,
            'language' => $language,
            'total' => $repos->count(),
            'stars' => $repos->sum('stars'),
            'lastSynced' => $repos->max('updated_at'),
        ]);
    }
}
