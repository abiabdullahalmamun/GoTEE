<?php

namespace App\Exceptions;

use Exception;

class ExceptionHandler extends Exception
{
    public function __construct($message = 'Somthing wrong. please try again later', $code = 401,Exception $prev = null)
    {
        parent::__construct($message, $code, $prev);
    }
}
