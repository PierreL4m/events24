<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HostRepository")
 * @UniqueEntity(fields="name", message="Ce nom existe déjà, merci d'en choisir un autre")
 * 
 */
class Host
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EventType", mappedBy="hosts")
     */
    private $eventTypes;

    public function __construct()
    {
        $this->eventTypes = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|EventType[]
     */
    public function getEventTypes(): Collection
    {
        return $this->eventTypes;
    }

    public function addEventType(EventType $eventType): self
    {
        if (!$this->eventTypes->contains($eventType)) {
            $this->eventTypes[] = $eventType;
            $eventType->addHost($this);
        }

        return $this;
    }

    public function removeEventType(EventType $eventType): self
    {
        if ($this->eventTypes->contains($eventType)) {
            $this->eventTypes->removeElement($eventType);
            $eventType->removeHost($this);
        }

        return $this;
    }
}
