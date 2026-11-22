<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeEntry extends Model
{
    /** List statuses in display order => label. */
    public const STATUSES = [
        'watching' => 'Watching',
        'completed' => 'Completed',
        'on_hold' => 'On hold',
        'dropped' => 'Dropped',
        'plan_to_watch' => 'Plan to watch',
    ];

    protected $fillable = [
        'mal_id',
        'title',
        'image_url',
        'media_type',
        'num_episodes',
        'mean',
        'status',
        'score',
        'episodes_watched',
        'is_rewatching',
        'list_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'mal_id' => 'integer',
            'num_episodes' => 'integer',
            'mean' => 'float',
            'score' => 'integer',
            'episodes_watched' => 'integer',
            'is_rewatching' => 'boolean',
            'list_updated_at' => 'datetime',
        ];
    }

    public function malUrl(): string
    {
        return "https://myanimelist.net/anime/{$this->mal_id}";
    }

    /** Watched share as a percentage, or null when the episode count isn't known yet. */
    public function progressPercent(): ?float
    {
        if ($this->num_episodes < 1) {
            return null;
        }

        return min(100, round($this->episodes_watched / $this->num_episodes * 100, 1));
    }
}
