<?php

namespace App\EventListener;

use App\Entity\CandidateUser;
use App\Entity\CandidateParticipation;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class RemoveFilesListener
{
	
    private $logger;
    private $public_dir;

	public function __construct(LoggerInterface $logger, $public_dir)
	{
		$this->public_dir = $public_dir;
        $this->logger = $logger;
    }
   
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
 
        if ($entity instanceof CandidateParticipation) { 
            $invitation = $this->public_dir.$entity->getInvitationPath();
            if (file_exists($invitation) && is_file($invitation)){
                unlink($invitation);
            }
        }
        else if ($entity instanceof CandidateUser) { 
            $cv = $this->public_dir.$entity->getCvPath();
           
            if (file_exists($cv) && is_file($cv)){
                unlink($cv);
            }
        }
    }
}