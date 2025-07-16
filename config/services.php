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

    'mailgun'  => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'location' => [
        'cache_duration' => 24 * 60 * 60, // 24 hours
        'timeout'        => 5,            // 5 seconds
        'apis'           => [
            'ip_api'    => [
                'url'     => 'http://ip-api.com/json/',
                'enabled' => true,
            ],
            'ipapi_co'  => [
                'url'     => 'https://ipapi.co/',
                'enabled' => true,
            ],
            'ipinfo'    => [
                'url'     => 'https://ipinfo.io/',
                'enabled' => true,
            ],
            'freeipapi' => [
                'url'     => 'https://freeipapi.com/api/json/',
                'enabled' => true,
            ],
        ],
    ],
];
