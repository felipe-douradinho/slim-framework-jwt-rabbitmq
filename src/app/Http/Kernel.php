<?php

use Slim\App;

return function(App $app) {

    $is_sapi = is_cli(); // avoid problems with migration when CLI

    $errorMiddleware = $app->addErrorMiddleware(
        $is_sapi ? false : config('error_details.displayErrorDetails'),
        $is_sapi ? false : config('error_details.logErrorDetails'),
        $is_sapi ? false : config('error_details.logErros'),
    );

    // Error Handler
    $errorHandler = $errorMiddleware->getDefaultErrorHandler();
    $errorHandler->forceContentType('application/json');

};