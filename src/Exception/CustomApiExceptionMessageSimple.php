<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class CustomApiExceptionMessageSimple extends HttpException
{
    public function __construct($message, $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY, $code = 0, \Exception $previous = null) {
        parent::__construct($statusCode, $message, $previous, [], $code);
    }
}