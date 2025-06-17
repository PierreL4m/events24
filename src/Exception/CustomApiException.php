<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class CustomApiException extends HttpException
{
    public function __construct($message, array $errors = [], $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY, $code = 0, \Exception $previous = null) {
        parent::__construct($statusCode, json_encode(['message' => $message, 'errors' => $errors]), $previous, [], $code);
    }
}