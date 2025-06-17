<?php

namespace App\Helper;

use App\Entity\Event;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Factory\ParticipationFactory;
use Doctrine\ORM\EntityManagerInterface;

class ParticipationHelper
{
    private $em;
    private $factory;

    public function __construct(EntityManagerInterface $em, ParticipationFactory $factory)
    {
        $this->em = $em;
        $this->factory = $factory;
    }

    public function generateParticipation(Event $event, Organization $organization, $form)
    {
        $exists = $this->em->getRepository(Participation::class)->findByOrganizationAndEvent($organization,$event);

        if (count($exists) > 0 ){
            $form->addError(new FormError('Vous ne pouvez pas ajouter deux fois '.$organization->getName().' au même événement'));
            return false;
        }
        else{
            $participation = $this->factory->get($event,$organization);
            $previous = $this->em->getRepository(Participation::class)->getPrevious($organization);                   
            $previous_same_type = $this->em->getRepository(Participation::class)->getPreviousSameType($organization, $participation);

            if (!$previous){
                $participation->setCompanyName($organization->getName());
                $participation->setOrganization($organization);
            }
            else{
                $participation->copy($previous);
                $participation->copyAndCheckType($previous_same_type);
            }
                             
            $participation->setEvent($event);
            $this->em->persist($participation);

            return true;
        }
    }
}