<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;

final class RegistrationFormException extends \Exception
{
    public $errors;

    public function __construct($message,
                                array $messages = [],
                                $code = 0,
                                \Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $messages;
    }
}