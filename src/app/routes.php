<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {

    // -- register the user
    $app->post('/api/v1/users', \App\Http\Controllers\UsersController::class . ':store');

    // -- get the stocks
    $app->get('/api/v1/stocks', \App\Http\Controllers\StocksController::class . ':index');

    // -- get the quotes
    $app->get('/api/v1/quotes', \App\Http\Controllers\QuotesController::class . ':index');

};