<?php

return [
    'error_details' => [
        'displayErrorDetails' => (env('ENV', 'prod') == 'dev'),
        'logErrorDetails' => (env('ENV', 'prod') == 'dev'),
        'logErros' => (env('ENV', 'prod') == 'dev'),
    ]
];