<?php

namespace App\Helper;

use App\Entity\CandidateParticipation;
use App\Entity\Event;
use App\Entity\OrganizationType;
use Doctrine\ORM\EntityManagerInterface;

class H48Helper
{

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}
    public function is48(Event $event)
    {
        if ($event->getType() == '48'){
            return true;
        }
        return false;
    }
    
    public function isMain48(Event $event)
    {
        if ($event->getType() != '48'){
            return false;
        }
        
        if(!($main =$this->getMain48($event, false))) {
            return false;
        }
        
        if($main->getId() == $event->getId()) {
            return true;
        }
        
        return false;
        
    }

    //return false if !48 event or 48 formation
    public function is48Formation(Event $event)
    {
        if ($this->is48($event)){
            $organization_types = $event->getOrganizationTypes() ;
            $formation = $this->em->getRepository(OrganizationType::class)->findOneBySlug('formation');

            if ($organization_types->contains($formation)){
                return true;
            }
        }
        return false;
    }

    public function is48Emploi(Event $event)
    {
        if ($this->is48($event)){
            $organization_types = $event->getOrganizationTypes() ;
            $company = $this->em->getRepository(OrganizationType::class)->findOneBySlug('company');

            if ($organization_types->contains($company)){
                return true;
            }
        }
        return false;
    }
    
    public function getMain48(Event $event,$check=true)
    {
        if($check){
            if (!$this->is48($event)){
                throw new \Exception('Cannot call getMain48 on non 48 event');
            }
        }
        return $this->em->getRepository(Event::class)->getMain48($event);
    }
    
    public function getSecond48(Event $event,$check=true)
    {
        if($check){
            if (!$this->is48($event)){
                throw new \Exception('Cannot call getSecond48 on non 48 event');
            }
        }
        return $this->em->getRepository(Event::class)->getSecond48($event);
    }

    public function getSecondParticipation(CandidateParticipation $participation)
    {
        $event = $participation->getEvent();
        if (!$this->is48($event)){
            throw new \Exception('Cannot call getSecondParticipation on non 48 candidate participation');
        }
        $second_event = $this->getSecond48($event);        

        if ($second_event){
            return $this->em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($participation->getCandidate(), $second_event) ;
        }
        return null;
    }
}