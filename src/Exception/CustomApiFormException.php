<?php
// api/src/Exception/ProductNotFoundException.php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Form\FormInterface;

final class CustomApiFormException extends CustomApiException
{
    
    public function __construct(array $normalizedForm, $statusCode = Response::HTTP_BAD_REQUEST, $code = 0, \Exception $previous = null) {
        parent::__construct($normalizedForm['title'], array('errors'=>$normalizedForm['errors'], 'children' => $normalizedForm['children']), $statusCode, $code, $previous);
    }
}