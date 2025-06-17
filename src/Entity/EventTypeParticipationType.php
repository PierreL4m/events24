<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTypeParticipationTypeRepository")
 */
class EventTypeParticipationType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $participationClass;

    /**
     * @ORM\ManyToOne(targetEntity="EventType", inversedBy="participationTypes")
     */
    private $eventType;

     /**
     * @ORM\ManyToOne(targetEntity="OrganizationType", inversedBy="eventTypeParticipationTypes")
     */
    private $organizationType;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param mixed $eventType
     *
     * @return self
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganizationType()
    {
        return $this->organizationType;
    }

    /**
     * @param mixed $organizationType
     *
     * @return self
     */
    public function setOrganizationType($organizationType)
    {
        $this->organizationType = $organizationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getParticipationClass()
    {
        return $this->participationClass;
    }

    /**
     * @param string $participationClass
     *
     * @return self
     */
    public function setParticipationClass($participationClass)
    {
        $this->participationClass = $participationClass;

        return $this;
    }
}
