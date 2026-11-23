<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GithubRepo extends Model
{
    /** Adding this topic to a repository on GitHub puts it in the Featured section. */
    public const FEATURED_TOPIC = 'featured';

    /** GitHub's own language colours for the languages most likely to appear. */
    private const LANGUAGE_COLORS = [
        'PHP' => '#4F5D95',
        'Blade' => '#f7523f',
        'JavaScript' => '#f1e05a',
        'TypeScript' => '#3178c6',
        'Python' => '#3572A5',
        'Java' => '#b07219',
        'C#' => '#178600',
        'C++' => '#f34b7d',
        'C' => '#555555',
        'HTML' => '#e34c26',
        'CSS' => '#663399',
        'Vue' => '#41b883',
        'Go' => '#00ADD8',
        'Rust' => '#dea584',
        'Shell' => '#89e051',
        'Lua' => '#000080',
        'Kotlin' => '#A97BFF',
        'Swift' => '#F05138',
    ];

    protected $fillable = [
        'github_id',
        'name',
        'full_name',
        'description',
        'html_url',
        'homepage',
        'language',
        'stars',
        'forks',
        'topics',
        'is_archived',
        'pushed_at',
        'repo_created_at',
    ];

    protected function casts(): array
    {
        return [
            'github_id' => 'integer',
            'stars' => 'integer',
            'forks' => 'integer',
            'topics' => 'array',
            'is_archived' => 'boolean',
            'pushed_at' => 'datetime',
            'repo_created_at' => 'datetime',
        ];
    }

    public function isFeatured(): bool
    {
        return in_array(self::FEATURED_TOPIC, $this->topics ?? [], true);
    }

    /** Topics to display, without the internal "featured" marker. */
    public function displayTopics(): array
    {
        return array_values(array_diff($this->topics ?? [], [self::FEATURED_TOPIC]));
    }

    /** "SteamApplication-" → "Steam Application", "E-Commerce-Website" → "E Commerce Website". */
    public function displayName(): string
    {
        $name = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $this->name);

        return trim(preg_replace('/[-_]+/', ' ', $name));
    }

    public function languageColor(): string
    {
        return self::LANGUAGE_COLORS[$this->language] ?? '#8b949e';
    }
}
