<?php

return [

    'default' => 'main',

    'channels' => [

        'main' => [
            'driver' => 'stack',
            'channels' => ['single'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => 'logs/laravel.log',
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => 'logs/laravel.log',
            'level' => 'debug',
            'days' => 7,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'stream' => [
            'driver' => 'stream',
            'stream' => 'php://stderr',
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],
    ],
];