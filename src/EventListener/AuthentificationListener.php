<?php
namespace App\EventListener;

use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthentificationListener {
    /**
     * 
     * @var SessionInterface
     */
    private SessionInterface $session;
    
    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }
        
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $e) {
        
        $token = $e->getAuthenticationToken();
        
        // FIXME : we had to do this only to have a working is_granted in email templates from an api context
        // no idea why, as it seems to be at least partially deprecated
        if($token instanceof OAuth2Token) {
            $token->setAuthenticated(true, false);
        }
        
        // we got a token, probably with json_login
        if($token instanceof UsernamePasswordToken) {
            $this->session->set('jobflix_useridentifier', $token->getUser()->getUserIdentifier());
        }
    }
} 