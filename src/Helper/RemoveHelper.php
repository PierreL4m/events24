<?php

namespace App\Helper;

use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Participation;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RemoveHelper
 {
    private $logger;
    private $session;
    private $token_storage;

	public function __construct(LoggerInterface $logger, SessionInterface $session, TokenStorageInterface $token_storage)
	{
        $this->logger = $logger;
        $this->session = $session;
        $this->token_storage = $token_storage;
	}

//public useless?
//do not show button if cannot delete...
    public function canDelete($entity,$public=false)
    {
        $user = $this->token_storage->getToken()->getUser();

        if ($user->hasRole('ROLE_SUPER_ADMIN')){
          return true;
        }

        if ($entity instanceof EventType) {
           $places = $entity->getPlaces();

           if(count($places) > 0){
           		if (!$public){
           			$this->session->getFlashBag()->add('danger', 'Vous ne pouvez pas supprimer un type d\'événement qui est associé à un lieu');
           		}
                return false;
           }
           else{
           		return true;
           }
        }
        if ($entity instanceof Event) {

          if ($entity->getOnline() <= new \Datetime()){
            return false;
          }

         $participations = $entity->getParticipations();

         if(count($participations) > 0){
            if (!$public){
              $this->session->getFlashBag()->add('danger', 'Vous ne pouvez pas supprimer un événement qui contient des participations');
            }
              return false;
         }
         else{
            return true;
         }
      }
      if ($entity instanceof Participation && $entity->getEvent()) {
           $date = $entity->getEvent()->getDate();

           if($date < new \Datetime){
                return false;
           }
           else{
              return true;
           }
        }
   }
}