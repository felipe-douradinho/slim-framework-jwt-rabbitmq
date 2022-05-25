<?php

return [
    'auth_basic' => [
        "secure" => !(env('ENV', 'prod') == 'dev'), // false if env == dev
        "path" => '/',
        "ignore" => [ '/api/v1/users', '/api/v1/auth' ],

        "users" => [
            (env("ADMIN_USERNAME") ?? 'root') => (env("ADMIN_PASSWORD") ?? 'secret'),
        ],
    ]
];