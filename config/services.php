<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'steam' => [
        // Web API key from https://steamcommunity.com/dev/apikey
        'key' => env('STEAM_API_KEY'),
        // 17-digit SteamID64 of the profile to show. Its game details must be public.
        'steam_id' => env('STEAM_ID'),
    ],

    'myanimelist' => [
        // Client ID from https://myanimelist.net/apiconfig
        'client_id' => env('MAL_CLIENT_ID'),
        // Username whose anime list to show. The list must be public.
        'username' => env('MAL_USERNAME'),
    ],

    'spotify' => [
        // App credentials from https://developer.spotify.com/dashboard
        'client_id' => env('SPOTIFY_CLIENT_ID'),
        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
        // Must match a Redirect URI in the Spotify app. Defaults to this site's /spotify/callback.
        'redirect' => env('SPOTIFY_REDIRECT_URI'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
