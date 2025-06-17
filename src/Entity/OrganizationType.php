<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;


/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationTypeRepository")
 */
class OrganizationType
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
     * @ORM\Column(type="string", length=255, unique=true)
     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="EventTypeParticipationType", mappedBy="organizationType", cascade={"persist"})
     */
    private $eventTypeParticipationTypes;

    /**
     * @ORM\ManyToMany(targetEntity="Organization",inversedBy="organizationTypes")
     * @ORM\JoinTable()
     */
    private $organizations;

    /**
     * @ORM\ManyToMany(targetEntity="Event",inversedBy="organizationTypes")
     * @ORM\JoinTable()
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="EventType",inversedBy="organizationTypes")
     * @ORM\JoinTable()
     */
    private $eventTypes;

     /**
     * Constructor
     */
    public function __construct()
    {       
        $this->eventTypes = new ArrayCollection();
        $this->organizations = new ArrayCollection();
        $this->eventTypeParticipationTypes = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name ;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

     /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }
    /**
     * Add eventTypes
     *
     * @param \App\Entity\EventTypes $eventTypes
     *
     * @return Event
     */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;      

        return $this;
    }

    /**
     * Remove eventTypes
     *
     * @param \App\Entity\EventTypes $eventTypes
     */
    public function removeEvent(\App\Entity\Event $event)
    {
        $this->events->removeElement($event);
    }


    /**
     * @return mixed
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }
    /**
     * Add organization
     *
     * @param \App\Entity\Organization $organization
     *
     * @return Event
     */
    public function addOrganization(\App\Entity\Organization $organization)
    {
        $this->organizations[] = $organization;

        return $this;
    }

    /**
     * Remove organization
     *
     * @param \App\Entity\Organization $organization
     */
    public function removeOrganization(\App\Entity\Organization $organization)
    {
        $this->organizations->removeElement($organization);
    }

    /**
     * @return mixed
     */
    public function getEventTypeParticipationTypes()
    {
        return $this->eventTypeParticipationTypes;
    }
    /**
     * Add eventTypeParticipationType
     *
     * @param \App\Entity\eventTypeParticipationType $eventTypeParticipationType
     *
     * @return Event
     */
    public function addEventTypeParticipationType(\App\Entity\EventTypeParticipationType $eventTypeParticipationType)
    {
        $this->eventTypeParticipationTypes[] = $eventTypeParticipationType;
        $eventTypeParticipationType->setOrganizationType($this);

        return $this;
    }

    /**
     * Remove eventTypeParticipationType
     *
     * @param \App\Entity\eventTypeParticipationType $eventTypeParticipationType
     */
    public function removeEventTypeParticipationType(\App\Entity\EventTypeParticipationType $eventTypeParticipationType)
    {
        $this->eventTypeParticipationTypes->removeElement($eventTypeParticipationType);
    }

    /**
     * @return mixed
     */
    public function getEventTypes()
    {
        return $this->eventTypes;
    }
    /**
     * Add eventtypeTypes
     *
     * @param \App\Entity\EventTypeTypes $eventtypeTypes
     *
     * @return EventType
     */
    public function addEventType(\App\Entity\EventType $eventtype)
    {
        $this->eventTypes[] = $eventtype;      

        return $this;
    }

    /**
     * Remove eventtypeTypes
     *
     * @param \App\Entity\EventTypeTypes $eventtypeTypes
     */
    public function removeEventType(\App\Entity\EventType $eventtype)
    {
        $this->eventTypes->removeElement($eventtype);
    }
  
}
