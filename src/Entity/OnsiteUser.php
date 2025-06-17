<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OnsiteUserRepository")
 */
class OnsiteUser extends User
{
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sendPassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="manager", cascade={"persist"})
     */
    private $events;

    public function __construct()
    {
        parent::__construct();
        $this->events = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->firstname;
    }

    public function getFullName()
    {
        return parent::__toString();
    }

    public function getType()
    {
        return 'onsite';
    }
    public function getSendPassword(): ?bool
    {
        return $this->sendPassword;
    }

    public function setSendPassword(?bool $sendPassword): self
    {
        $this->sendPassword = $sendPassword;

        return $this;
    }

    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return User
     */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;

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

    public function getEmailBises()
    {
        return null;
    }
}
