<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SteamGame extends Model
{
    protected $fillable = [
        'appid',
        'name',
        'playtime_forever',
        'playtime_2weeks',
        'last_played_at',
    ];

    protected function casts(): array
    {
        return [
            'appid' => 'integer',
            'playtime_forever' => 'integer',
            'playtime_2weeks' => 'integer',
            'last_played_at' => 'datetime',
        ];
    }

    /** Games played in the last two weeks, most played first. */
    public function scopeRecentlyPlayed(Builder $query): Builder
    {
        return $query->where('playtime_2weeks', '>', 0)->orderByDesc('playtime_2weeks');
    }

    public function headerImageUrl(): string
    {
        return "https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps/{$this->appid}/header.jpg";
    }

    public function storeUrl(): string
    {
        return "https://store.steampowered.com/app/{$this->appid}";
    }

    public static function hours(?int $minutes): string
    {
        return number_format(($minutes ?? 0) / 60, 1);
    }
}
