<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Cookie;

class ResponseListener {
    
    private SessionInterface $session;
    
    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }
    
    public function onKernelResponse(ResponseEvent $event) {
        $response = $event->getResponse();
        // create session identifier if needed
        if($this->session->has('jobflix_session_identifier')) {
            $identifier = $this->session->get('jobflix_session_identifier');
        }
        else {
            // useful for api anonymous access with a browser
            $identifier = ByteString::fromRandom(32)->toString();
        }
        $this->session->set('jobflix_session_identifier', $identifier);
        $response->headers->setCookie(new Cookie('_jobflix', $identifier));
        // send cookie to tell JS app that we're "initialized"
        $response->headers->setCookie(new Cookie('_jf_ts', time(), 0, '/', null, null, false));
        
    }
} 