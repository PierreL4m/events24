<?php
namespace App\Logger;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionRequestProcessor
{
    private $session;
    private $request_stack;

    public function __construct(SessionInterface $session, RequestStack $request_stack)
    {
        $this->session = $session;
        $this->request_stack = $request_stack;
    }


    
    public function processRecord(array $record)
    {
        $request = $this->request_stack->getCurrentRequest();    
      
        if ($request){
            $record['extra']['headers'] = $request->headers->all();
            $record['extra']['query'] = $request->getMethod();
        }

        return $record;
    }
}