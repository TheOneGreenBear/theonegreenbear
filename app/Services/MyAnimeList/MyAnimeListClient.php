<?php

namespace App\Services\MyAnimeList;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Reads a user's public anime list from the official MyAnimeList API v2.
 *
 * @see https://myanimelist.net/apiconfig/references/api/v2
 */
class MyAnimeListClient
{
    private const BASE_URL = 'https://api.myanimelist.net/v2';

    /** Safety cap on pages followed (1000 entries each). */
    private const MAX_PAGES = 20;

    public function __construct(
        private readonly ?string $clientId = null,
        private readonly ?string $username = null,
    ) {}

    public static function fromConfig(): self
    {
        return new self(config('services.myanimelist.client_id'), config('services.myanimelist.username'));
    }

    public function isConfigured(): bool
    {
        return filled($this->clientId) && filled($this->username);
    }

    /**
     * Every entry on the user's list.
     *
     * @return list<array{node: array<string, mixed>, list_status: array<string, mixed>}>
     */
    public function animeList(): array
    {
        $entries = [];
        $url = self::BASE_URL.'/users/'.rawurlencode($this->username).'/animelist';
        $query = [
            'fields' => 'list_status,num_episodes,mean,media_type',
            'limit' => 1000,
            'nsfw' => 'true',
        ];

        for ($page = 0; $url && $page < self::MAX_PAGES; $page++) {
            $response = Http::timeout(15)
                ->retry(2, 500, throw: false)
                ->withHeaders(['X-MAL-CLIENT-ID' => $this->clientId])
                ->get($url, $query)
                ->throw();

            array_push($entries, ...$response->json('data', []));

            // "next" is a full URL that already carries the query string. Pass no query at all
            // (not []), otherwise the HTTP client replaces the URL's query string with an empty one.
            $url = $response->json('paging.next');
            $query = null;
        }

        if ($url) {
            throw new RuntimeException('MyAnimeList list is larger than the page limit allows.');
        }

        return $entries;
    }
}
