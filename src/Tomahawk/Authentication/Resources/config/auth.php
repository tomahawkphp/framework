<?php

return [

    'default' => 'user',

    'guards' => [
        'user' => [
            'driver' => 'session',
            'provider' => 'users'
        ]
    ],

    'providers' => [
        'users' => [
            'driver' => 'memory',
        ],
    ],
];