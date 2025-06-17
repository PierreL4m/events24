<?php

namespace App\Factory;

use App\Entity\Event;
use App\Entity\EventTypeParticipationType;
use App\Entity\Organization;
use App\Entity\ParticipationCompanySimple;
use App\Entity\ParticipationDefault;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ParticipationFactory
{
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function getParticipation(Event $event, $organizationType, $organization)
    {        
        $event_type = $event->getType();
        $event_type_participation_type = $this->em->getRepository(EventTypeParticipationType::class)->findBy(
            array('eventType' => $event_type, 'organizationType' => $organizationType)
        );
        
        if (count($event_type_participation_type) == 1){
            $participationClass = "App\Entity\\".$event_type_participation_type[0]->getParticipationClass();

            return new $participationClass();
        }
        else{            
            $this->danger($organization);

            return new ParticipationCompanySimple();
        }
    }

    /**
     * get relevant participation instance
     *
     * @param Event $event
     * @param Organization $organization
     * @return Participation
     */
     public function get(Event $event,Organization $organization)
    {        

        if (!$organization->getOrganizationTypes()){
             throw new \Exception("L'exposant ".$organization->getName()." n\'a pas de type d'exposant");
        }

        if (!$event->getPlace()){
             throw new \Exception("L'événement ".$event->__toString().' n\'a pas de lieu');
        }
        if (!$event->getType()){
             throw new \Exception("L'événement ".$event.' n\'a pas de type d\'événement');
        }

        $event_type = $event->getType();

        $organizationTypes = $organization->getOrganizationTypes();

        if (count($organizationTypes) > 1){
            $accepted_organizations = $event->getOrganizationTypes();            
            $contains = 0;

            foreach ($organizationTypes as $type) {
                if($accepted_organizations->contains($type)){
                    $contains+=1;
                    $organization_type = $type;
                }               
            }
          
            if ($contains == 1){              
                return $this->getParticipation($event, $organization_type, $organization);
            }
            else{
                $this->danger($organization);

                return new ParticipationCompanySimple();
            }
        }
        elseif (count($organizationTypes) == 1){
            return $this->getParticipation($event, $organizationTypes[0], $organization);
        }
        
        throw new \Exception("Le type d'événement ".$event_type->__toString().' n\'a pas de participation définie pour le type d\'exposant '.$organizationTypes[0]);
    }

    public function danger($organization)
    {
        $this->session->getFlashBag()->add('danger', 'Merci de vérifier que le type de fiche pour l\'exposant '.$organization->__toString().' est bien de type "Emploi". S\'il participe en tant que centre de formation merci de changer sa fiche en "fiche formation"');
    }
}
?>