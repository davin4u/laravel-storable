<?php

return [
    'driver' => 'mongodb',

    'drivers' => [
        'mongodb' => [
            'user' => env('MONGODB_USER', 'MONGODB USER NAME'),
            'password' => env('MONGODB_PASSWORD', 'MONGODB USER PASSWORD'),
            'host' => env('MONGODB_HOST', 'localhost'),
            'port' => env('MONGODB_PORT', '27017'),
            'database' => env('MONGODB_DATABASE', 'MONGODB DATABASE NAME'),
            'collection' => env('MONGODB_DEFAULT_COLLECTION', 'default')
        ]
    ]
];
