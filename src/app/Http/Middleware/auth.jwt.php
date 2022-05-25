<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {

    $app->add(new Tuupola\Middleware\JwtAuthentication(array_merge(config('auth_jwt'), [

        "error" => function ($response, $arguments) {
            $data["status"] = \App\Helpers\ResponseHelper::STATUS_ERROR;
            $data["message"] = $arguments["message"];

            $response->getBody()->write(
                json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            );

            return $response->withHeader("Content-Type", "application/json");
        }

    ])));

};
