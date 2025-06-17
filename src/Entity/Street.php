<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\StreetRepository")
 */
class Street
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:get_participation"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:get_participation"})
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="street")
     */
    private $participations;

    public function __contruct()
    {
    	$this->participations = new ArrayCollection();
    }

    public function __toString()
    {
    	return $this->label;
    }

     /**
     * Add participation
     *
     * @param \App\Entity\Participation $participation
     *
     * @return Event
     */
    public function addParticipation(\App\Entity\Participation $participation)
    {
        $this->participations[] = $participation;
        $participation->setStreet($this);

        return $this;
    }

    /**
     * Remove participation
     *
     * @param \App\Entity\Participation $participation
     */
    public function removeParticipation(\App\Entity\Participation $participation)
    {
        $this->participations->removeElement($participation);
        $participation->setStreet(null);
    }

    /**
     * Get participations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
