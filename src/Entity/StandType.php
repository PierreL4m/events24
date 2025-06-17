<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StandTypeRepository")
 */
class StandType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $dimension;

    /**
     * @ORM\OneToOne(targetEntity="TechGuide", inversedBy="standType", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $techGuide;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="standType", cascade={"persist"})
     */
    private $participations;

    public function __toString()
    {
    	return string($this->dimension);
    }
    /**
     * Constructor
     */
    public function __construct()
    {       
        $this->participations = new ArrayCollection();
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
        $participation->setStandType($this);

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
        $participation->setStandType(null);
    }

    /**
     * @return mixed
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @return mixed
     */
    public function getTechGuide()
    {
        return $this->techGuide;
    }

    /**
     * @param mixed $techGuide
     *
     * @return self
     */
    public function setTechGuide($techGuide)
    {
        $this->techGuide = $techGuide;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @param mixed $dimension
     *
     * @return self
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
