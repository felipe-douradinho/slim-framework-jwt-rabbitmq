<?php

declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Tuupola\Middleware\HttpBasicAuthentication;

return function (App $app) {

    // 1st middleware to configure basic authentication
    $app->add(new HttpBasicAuthentication(array_merge(config('auth_basic'), [
        "error" => function ($response) {
            return $response->withStatus(401);
        }
    ])));

    // 2nd middleware to throw 401 with correct slim exception
    // Reformat when lin updates to v4, see: https://github.com/tuupola/slim-basic-auth/issues/95
    $app->add(function (Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);
        $statusCode = $response->getStatusCode();

        if ($statusCode == 401) {
            $data["status"] = \App\Helpers\ResponseHelper::STATUS_ERROR;
            $data["message"] = 'Unauthorized';

            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );

            return $response->withHeader("Content-Type", "application/json");
        }

        return $response;
    });
};
