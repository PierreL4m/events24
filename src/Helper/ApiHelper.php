<?php

namespace App\Helper;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Context\Context;
use Symfony\Component\Form\FormInterface;
use App\Exception\CustomApiFormException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\CustomApiException;
use Symfony\Component\HttpFoundation\Request;

class ApiHelper
{
    /**
     * 
     * @var NormalizerInterface
     */
    private NormalizerInterface $normalizer;
    
    public function __construct(NormalizerInterface $normalizer) {
        $this->normalizer = $normalizer;
    }
    
    /**
     * 
     * @param string $message
     * @param int $statusCode
     * @throws CustomApiException
     */
    public function apiException(string $message,array $error=[], $statusCode = Response::HTTP_BAD_REQUEST) {
        throw new CustomApiException($message,$error, $statusCode);
    }
    
    /**
     * 
     * @param FormInterface $form
     * @param int $statusCode
     * @throws CustomApiFormException
     */
    public function formException(FormInterface $form, $statusCode = Response::HTTP_BAD_REQUEST) {
        throw new CustomApiFormException($this->normalizer->normalize($form), $statusCode);
    }
    
    /**
     * 
     * @param Request $request
     * @return Request
     */
    public function getAdaptedRequest(Request $request) :Request {
        return $request->duplicate(null, json_decode($request->getContent(), true));
    }
}
	