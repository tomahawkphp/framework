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
    'default' => 'filesystem',


    'stores' => [

        'array' => [
            'driver' => 'array',
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'filesystem' => [
            'driver' => 'filesystem',
            'directory' => __DIR__ .'/../../storage/cache/application',
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => 'memcached_id',
            'sasl' => [
                'memcached_username',
                'memcached_password',
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT  => 2000,
            ],
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 100,
                ],
            ],
        ],
    ],

    /*
     * Prefix
     */
    'prefix' => 'tomahawk_cache',
];
