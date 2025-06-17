<?php
namespace App\EventListener;

use League\Bundle\OAuth2ServerBundle\Event\UserResolveEvent;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OauthUserResolveListener {
    
    /**
     * 
     * @var UserRepository
     */
    private $repo;
    
    /**
     * 
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;
    
    public function __construct(UserRepository $repo, UserPasswordHasherInterface $userPasswordHasher) {
        $this->repo = $repo;
        $this->userPasswordHasher = $userPasswordHasher;
    }
        
    public function onUserResolve(UserResolveEvent $e) {
        $user = $this->repo->loadUserByIdentifier($e->getUsername());
        
        if(!$user) {
            return null;
        }
        
        if(!$this->userPasswordHasher->isPasswordValid($user, $e->getPassword())) {
            return null;
        }
        $e->setUser($user);
    }
}