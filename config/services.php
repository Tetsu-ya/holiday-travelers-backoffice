<?php

return [

    'http' => [
        'ca_bundle' => env('HTTP_CA_BUNDLE', 'C:\\xampp\\apache\\bin\\curl-ca-bundle.crt'),
    ],

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'allowed_domains' => array_values(array_filter(array_map(
            'strtolower',
            array_map('trim', explode(',', env('GOOGLE_ALLOWED_DOMAINS', ''))),
        ))),
        'allowed_emails' => array_values(array_filter(array_map(
            'strtolower',
            array_map('trim', explode(',', env('GOOGLE_ALLOWED_EMAILS', ''))),
        ))),
    ],

    'maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
        'origin' => env('GOOGLE_MAPS_ORIGIN', 'Manila, Philippines'),
    ],

    'tomtom' => [
        'key' => env('TOMTOM_API_KEY'),
        'origin' => env('TOMTOM_ORIGIN', 'Manila, Philippines'),
        'routing_url' => env('TOMTOM_ROUTING_URL', 'https://api.tomtom.com/routing/1/calculateRoute'),
        'search_url' => env('TOMTOM_SEARCH_URL', 'https://api.tomtom.com/search/2/search'),
    ],

    'weather' => [
        'base_url' => env('WEATHER_BASE_URL', 'https://api.open-meteo.com/v1/forecast'),
        'geocoding_url' => env('WEATHER_GEOCODING_URL', 'https://geocoding-api.open-meteo.com/v1/search'),
    ],

];
