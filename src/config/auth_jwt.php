<?php

return [
    'auth_jwt' => [
        "secure" => !(env('ENV', 'prod') == 'dev'), // false if env == dev
        "secret" => env("JWT_SECRET"),
        "path" => '/',
        "ignore" => [ '/api/v1/users', '/api/v1/auth', '/teste' ],
    ]
];