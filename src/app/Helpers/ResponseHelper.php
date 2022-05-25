<?php

namespace App\Helpers;


class ResponseHelper
{
    const EXTERNAL_SERVICE_UNAVAILABLE      = 503;
    const BAD_REQUEST                       = 400;
    const EXTERNAL_SERVICE_BAD_RESPONSE     = 502;
    const INTERNAL_SERVER_ERROR             = 500;
    const NOT_FOUND                         = 404;

    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    public static array $statuses = [
        self::STATUS_ERROR,
        self::STATUS_SUCCESS,
    ];

    public static array $errors_codes = [
        self::EXTERNAL_SERVICE_UNAVAILABLE,
        self::BAD_REQUEST,
        self::EXTERNAL_SERVICE_BAD_RESPONSE,
        self::NOT_FOUND,
    ];

}