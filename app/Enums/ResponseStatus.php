<?php

namespace App\Enums;

enum ResponseStatus: string
{
    const SUCCESS = 'success';
    const ERROR = 'error';
    const VALIDATION_ERROR = 'Ops! validation failed.';
    const UNAUTHORIZED = 'unauthorized';
    const FORBIDDEN = 'forbidden';
    const NOT_FOUND = 'Data not found';
    const SERVER_ERROR = 'Something went wrong.';

    const CODE_OK = 200;
    const CODE_CREATED = 201;
    const CODE_ACCEPTED = 202;
    const CODE_NO_CONTENT = 204;
    const CODE_MOVED_PERMANENTLY = 301;
    const CODE_FOUND = 302;
    const CODE_NOT_MODIFIED = 304;
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_METHOD_NOT_ALLOWED = 405;
    const CODE_CONFLICT = 409;
    const CODE_UNPROCESSABLE_ENTITY = 422;
    const CODE_INTERNAL_SERVER_ERROR = 500;
    const CODE_NOT_IMPLEMENTED = 501;
    const CODE_BAD_GATEWAY = 502;
    const CODE_SERVICE_UNAVAILABLE = 503;
}
