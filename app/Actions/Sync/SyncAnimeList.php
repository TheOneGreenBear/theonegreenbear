<?php

namespace App\Actions\Sync;

use App\Models\AnimeEntry;
use App\Services\MyAnimeList\MyAnimeListClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncAnimeList
{
    public function __construct(private readonly MyAnimeListClient $client) {}

    /**
     * Mirror the MyAnimeList list into the anime_entries table.
     *
     * @return int|null Number of entries stored, or null when MyAnimeList isn't configured.
     */
    public function __invoke(): ?int
    {
        if (! $this->client->isConfigured()) {
            return null;
        }

        // Fetch everything first, so a failed request leaves the old data intact.
        $rows = collect($this->client->animeList())->map(function (array $entry) {
            $node = $entry['node'];
            $status = $entry['list_status'] ?? [];

            return [
                'mal_id' => $node['id'],
                'title' => $node['title'],
                'image_url' => $node['main_picture']['large'] ?? $node['main_picture']['medium'] ?? null,
                'media_type' => $node['media_type'] ?? null,
                'num_episodes' => $node['num_episodes'] ?? 0,
                'mean' => $node['mean'] ?? null,
                'status' => $status['status'] ?? 'plan_to_watch',
                'score' => $status['score'] ?? 0,
                'episodes_watched' => $status['num_episodes_watched'] ?? 0,
                'is_rewatching' => $status['is_rewatching'] ?? false,
                'list_updated_at' => isset($status['updated_at']) ? Carbon::parse($status['updated_at']) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::transaction(function () use ($rows) {
            // Entries removed from the list on MAL are removed here too.
            AnimeEntry::whereNotIn('mal_id', $rows->pluck('mal_id'))->delete();

            foreach ($rows->chunk(200) as $chunk) {
                AnimeEntry::upsert(
                    $chunk->values()->all(),
                    uniqueBy: ['mal_id'],
                    update: ['title', 'image_url', 'media_type', 'num_episodes', 'mean', 'status', 'score', 'episodes_watched', 'is_rewatching', 'list_updated_at', 'updated_at'],
                );
            }
        });

        return $rows->count();
    }
}
