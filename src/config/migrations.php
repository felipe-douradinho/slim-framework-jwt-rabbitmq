<?php


require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once base_path('public/index.php');


return [

    'paths' => [
        'migrations'    => base_path('database/migrations'),
        'seeds'         => base_path('database/seeders'),
    ],

    'environments' => [
        'default_migration_table' => 'migrations',
        env('env', 'env') => [
            'adapter'   => env('DB_CONNECTION', 'mysql'),
            'host'      => env('DB_HOST', '127.0.0.1'),
            'name'      => env('DB_DATABASE', 'forge'),
            'user'      => env('DB_USERNAME', 'forge'),
            'pass'      => env('DB_PASSWORD', ''),
            'port'      => env('DB_PORT', '3306')
        ]
    ]

];