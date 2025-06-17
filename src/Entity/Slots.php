<?php
namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SlotsRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path" = "/slots/event",
 *              "normalization_context"={"groups"={"read:collection_slots"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_slots"}}
 *           }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"event.id"})
 */
class Slots
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $maxCandidats;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $name;

    /**
     * @ORM\Column(type="time",nullable=false)
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $beginSlot;

    /**
     * @ORM\Column(type="time",nullable=false)
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $endingSlot;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="candidateParticipations")
     * @Groups({"read:slots_events"})
     */
    private $event;

    public function __toString()
    {
        return $this->name;
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
     * @return mixed
     */
    public function getBeginSlot()
    {
        return $this->beginSlot;
    }

    /**
     * @param mixed $beginSlot
     *
     * @return self
     */
    public function setBeginSlot($beginSlot)
    {
        $this->beginSlot = $beginSlot;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndingSlot()
    {
        return $this->endingSlot;
    }

    /**
     * @param mixed $endingSlot
     *
     * @return self
     */
    public function setEndingSlot($endingSlot)
    {
        $this->endingSlot = $endingSlot;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxCandidats()
    {
        return $this->maxCandidats;
    }

    /**
     * @param mixed $maxCandidats
     *
     * @return self
     */
    public function setMaxCandidats($maxCandidats)
    {
        $this->maxCandidats = $maxCandidats;

        return $this;
    }

    /**
     * Get events
     *
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return EventType
     */
    public function setEvent(\App\Entity\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:collection_slots"})
     * @Groups({"read:slots_events"})
     */
    private $is_full;

    /**
     * @param bool $is_full
     *
     * @return self
     */
    public function setis_full($is_full)
    {
        $this->is_full = $is_full;

        return $this;
    }

    /**
     * @return bool
     */
    public function isis_full()
    {
        return $this->is_full;
    }
    
    /**
     * @return bool
     */
    public function getIsFull()
    {
        return $this->is_full;
    }
}
