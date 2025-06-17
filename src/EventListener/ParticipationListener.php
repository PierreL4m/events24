<?php

namespace App\EventListener;

use App\Entity\Participation;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ParticipationListener
{
    private $logger;
    private $token_storage;

	public function __construct(LoggerInterface $logger, TokenStorageInterface $token_storage)
	{
        $this->logger = $logger;
        $this->token_storage = $token_storage;
	}

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
 
        if ($entity instanceof Participation) {
            if ($this->token_storage && $this->token_storage->getToken()){
                $user = $this->token_storage->getToken()->getUser();
            }
            else{
                $user = null;
            }

            $this->logger->info('participation_delete');

            if($user && !$user->hasRole('ROLE_SUPER_ADMIN')) {
                $class = get_class($entity);
                $bkp = new $class;
                $bkp->copyFull($entity);
                $bkp->setCompanyName($entity->getCompanyName() . ' backup');

                $em = $args->getEntityManager();
                $em->persist($bkp);
                $em->flush();
            }
        }
        $exception = new Exception();
        $this->logger->error($exception->getTraceAsString());
    }
}