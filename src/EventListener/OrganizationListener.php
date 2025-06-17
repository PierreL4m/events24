<?php

namespace App\EventListener;

use App\Entity\ExposantScanUser;
use App\Entity\Organization;
use App\Helper\GlobalEmHelper;
use App\Helper\GlobalHelper;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OrganizationListener
{
    
    /**
     * 
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;
    
    /**
     * 
     * @var GlobalEmHelper
     */
    private GlobalEmHelper $globalEmHelper;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher, GlobalEmHelper $globalEmHelper)
    {
        $this->passwordHasher = $passwordHasher;
        $this->globalEmHelper = $globalEmHelper;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!$entity instanceof Organization) {
                return;
            }              
            
            $name = $entity->getName();
            $user = new ExposantScanUser();
            $user->setUsername($name);
            $name = $this->globalEmHelper->generateUsername($user, $em);
            $password = $this->globalEmHelper->generateRandomPassword($user, $em);

            $user->setFirstname($name);
            $user->setUsername($name);
            $user->setLastname($name);
            $user->setEmail($name);
            $user->setPhone('0320202020');
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $user->setRoles(['ROLE_EXPOSANT_SCAN']);
            
            $user->setOrganization($entity);
            $em->persist($user);  
            $uow->computeChangeSets();
        }

    }
}