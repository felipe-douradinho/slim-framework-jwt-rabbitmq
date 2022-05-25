<?php

use Psr\Container\ContainerInterface;

return function(ContainerInterface $containerInterface) {

    $containerInterface->set('config', function () {
        $settings = [ ];
        $settings = require base_path('config/database.php');
        $error = require base_path('config/error.php');
        $auth_basic = require base_path('config/auth_basic.php');
        $auth_jwt = require base_path('config/auth_jwt.php');

        return array_merge($settings, $error, $auth_basic, $auth_jwt);
    });

};