<?php

return [

    'charset' => 'UTF-8',

    // Global variables to add to all templates
    'globals' => [
        'thing' => 'i am a thing'
    ],

    // Twig specific settings
    'twig' => [

        'debug'            => false,

        'cache'            => dirname(__DIR__) .'/../var/cache/twig',

        'strict_variables' => false,

        'autoescape'       => 'html',

        'optimizations'    => -1,
    ],
];
