<?php

return [

    /*
     * Cache Providers
     *
     * Supported:
     *      array
     *      database
     *      filesystem
     *      redis
     *      memcached
     *
     */
    'provider' => 'filesystem',


    'providers' => [

        'array' => [
            'driver' => 'array',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'file' => [
            'driver' => 'file',
            'path' => __DIR__ .'/../../storage/cache/application',
        ],
    ],

    /*
     * Prefix
     */
    'prefix' => 'tomahawk_cache',

    /*
     * Filesystem
     */
    'directory' => __DIR__ .'/../../storage/cache/application',
];
