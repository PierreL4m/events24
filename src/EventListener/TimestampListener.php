<?php

namespace App\EventListener;

use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\ParticipationSite;
use App\Entity\Timestamp;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TimestampListener
{
    private $token_storage;
    private $persist = null;

    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {        
        $entity = $args->getObject();

        if (!method_exists($entity, "setTimestamp")) {            
            return;
        }

        if ($this->token_storage->getToken()){ // this line is needed when executing phpunit
            $user = $this->token_storage->getToken()->getUser();
            $timestamp = new Timestamp();
            $timestamp->setCreated(new \DateTime());
            $timestamp->setCreatedBy($user);
            $entity->setTimestamp($timestamp);
        }
        $this->persist = true;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        if ($this->token_storage->getToken()){
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();

            $entities = array_merge(
                $uow->getScheduledEntityInsertions(),
                $uow->getScheduledEntityUpdates()
            );

            foreach ($entities as $entity) {
                // if ($entity instanceof ParticipationSite){
                //     $parent = $entity ;
                //     $entity = $entity->getParticipation();                  
                // } this does not work
                if (!method_exists($entity, "setTimestamp") or $this->persist or $entity instanceof Participation) {
                    return;
                }

                // if ($entity instanceof Participation && $entity->isTimestampDisabled()){
                //     return;
                // }
                if (!$entity->getTimestamp()){
                    $timestamp = new Timestamp();
                }
                else{
                    $timestamp = $entity->getTimestamp();
                }

                $user = $this->token_storage->getToken()->getUser();
                $timestamp->setUpdated(new \DateTime());
                $timestamp->setUpdatedBy($user);
                $entity->setTimestamp($timestamp);
                $em->persist($timestamp);
                $uow->computeChangeSets(); //This line invokes preFlush listener again ? 
                //this gives id to timestamp
           
            }
        }
    }
}