<?php

namespace App\Entity;

use App\Entity\ParticipantSection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionParticipantRepository")
 */
class SectionParticipant extends Section
{
    // public function getType()
    // {
    //     return 'Participant';
    // }
     /**
     * @ORM\OneToMany(targetEntity="ParticipantSection", mappedBy="section", cascade={"persist"})
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }
     /**
    }
    * Add participant
    *
    * @param \App\Entity\ParticipantSection $participant
    *
    * @return ParticipantType
    */
    public function addParticipant(\App\Entity\ParticipantSection $participant)
    {
        $this->participants[] = $participant;
        $participant->setSection($this);

        return $this;
    }

    /**
    * Remove participant
    *
    * @param \App\Entity\ParticipantSection $participant
    */
    public function removeParticipant(\App\Entity\ParticipantSection $participant)
    {
        $this->participants->removeElement($participant);
    }

    /**
    * Get participants
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getParticipants()
    {
        return $this->participants;
    }
}
