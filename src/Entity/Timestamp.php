<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimestampRepository")
 */
class Timestamp
{ 
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="creates")
     * @ORM\JoinColumn(nullable=true)
     */
    private $createdBy;

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="updates")
     * @ORM\JoinColumn(nullable=true)
     */
    private $updatedBy;

     /**
     * @ORM\OneToMany(targetEntity="Place", mappedBy="timestamp", cascade={"all"})
     */
    private $places;

     /**
     * @ORM\OneToMany(targetEntity="EventType", mappedBy="timestamp", cascade={"all"})
     */
    private $eventTypes;

     /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="timestamp", cascade={"all"})
     */
    private $events;

     /**
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="timestamp", cascade={"all"})
     */
    private $organizations;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="timestamp", cascade={"all"})
     */
    private $participations;

    public function __construct()
    {   
        $this->places = new ArrayCollection();
        $this->eventTypes = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->organizations = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }   

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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     *
     * @return self
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     *
     * @return self
     */
    public function setCreatedBy(\App\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     *
     * @return self
     */
    public function setUpdatedBy(\App\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
    * Add place
    *
    * @param \App\Entity\Place $place
    *
    * @return PlaceType
    */
    public function addPlace(\App\Entity\Place $place)
    {
        $this->places[] = $place;
        $place->setTimestamp($this);
    
        return $this;
    }

    /**
    * Remove place
    *
    * @param \App\Entity\Place $place
    */
    public function removePlace(\App\Entity\Place $place)
    {
        $this->places->removeElement($place);   
    }

    /**
    * Get places
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
    * Add eventtype
    *
    * @param \App\Entity\EventType $eventtype
    *
    * @return EventTypeType
    */
    public function addEventType(\App\Entity\EventType $eventtype)
    {
        $this->eventTypes[] = $eventtype;
        $eventtype->setTimestamp($this);
    
        return $this;
    }

    /**
    * Remove eventtype
    *
    * @param \App\Entity\EventType $eventtype
    */
    public function removeEventType(\App\Entity\EventType $eventtype)
    {
        $this->eventTypes->removeElement($eventtype);   
    }

    /**
    * Get eventtypes
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getEventTypes()
    {
        return $this->eventTypes;
    }

    /**
    * Add event
    *
    * @param \App\Entity\Event $event
    *
    * @return Event
    */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;
        $event->setTimestamp($this);
    
        return $this;
    }

    /**
    * Remove event
    *
    * @param \App\Entity\Event $event
    */
    public function removeEvent(\App\Entity\Event $event)
    {
        $this->events->removeElement($event);   
    }

    /**
    * Get events
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getEvents()
    {
        return $this->events;
    }

    /**
    * Add organization
    *
    * @param \App\Entity\Organization $organization
    *
    * @return Organization
    */
    public function addOrganization(\App\Entity\Organization $organization)
    {
        $this->organizations[] = $organization;
        $organization->setTimestamp($this);
    
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
    * Get organizations
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getOrganizations()
    {
        return $this->organizations;
    }

     /**
    * Add participation
    *
    * @param \App\Entity\Participation $participation
    *
    * @return Participation
    */
    public function addParticipation(\App\Entity\Participation $participation)
    {
        $this->participations[] = $participation;
        $participation->setTimestamp($this);
    
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
}
