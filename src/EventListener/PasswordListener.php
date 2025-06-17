<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PasswordListener
{
    private $logger;
    private $token_storage;
    
    /**
     * 
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $plainPassword = $entity->getPlainPassword();

            if (0 === strlen($plainPassword)) {
                return;
            }
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entity,
                $plainPassword
            );
            $entity->setPassword($hashedPassword);
            $entity->eraseCredentials();
        }
    }
}