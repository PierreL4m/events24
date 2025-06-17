<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\UniqueConstraint;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Place
 *
 * @ORM\Table(name="place", uniqueConstraints={@UniqueConstraint(name="place_unique", columns={"address", "cp"})})
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_place"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_place"}}
 *           }
 *     }
 * )
 */
class Place
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_mobile", type="string", length=255, nullable=true)
     * @Groups({"read:incomingEvents"})
     */
    private $nameMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Assert\NotNull()
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=5)
     * @Assert\NotNull()
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     * @Assert\NotNull()
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="string", length=255)
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="string", length=255)
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     */
    private $longitude;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     *
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="place")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Color", mappedBy="place",cascade={"all"},orphanRemoval=true)
     *
     *
     * @Assert\Valid
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $colors;

    /**
     * @ORM\ManyToOne(targetEntity="Timestamp", inversedBy="places", cascade={"all"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $timestamp;

    /**
     * @var FakeEventType
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     *
     *
     */
    private $eventType;

    /**
     * @var FakeEventType
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     *
     *
     */
    private $type;

    /**
     * @var \Array
     *
     * @ORM\ManyToOne(targetEntity="Region", cascade={"persist"})
     * @Assert\NotNull(message = "Merci de choisir la region")
     * @Assert\NotBlank(message = "Merci de choisir la region")
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:participation"})
     * @Groups({"read:incomingEvents"})
     */
    private $region;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->colors = new ArrayCollection();
    }

    public function setType()
    {
        if (empty($this->eventType)) {
            $this->eventType = new FakeEventType($this->city);
        }
        return $this->eventType;
    }

    /**
     *
     * @return \App\Entity\FakeEventType
     */
    public function getType()
    {
        if (empty($this->eventType)) {
            $this->eventType = new FakeEventType($this->city);
        }
        return $this->eventType;
    }

    public function __toString()
    {
        return $this->city;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Place
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add region
     *
     * @param \App\Entity\Region $region
     *
     * @return Region
     */
    public function setRegion(\App\Entity\Region $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Place
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Place
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Place
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Place
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return Place
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

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Place
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getNameMobile()
    {
        return $this->nameMobile;
    }

    /**
     * @param string $name_mobile
     *
     * @return self
     */
    public function setNameMobile($name_mobile)
    {
        $this->nameMobile = $name_mobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Add color
     *
     * @param \App\Entity\Color $color
     *
     * @return Place
     */
    public function addColor(\App\Entity\Color $color)
    {
        if ($this->colors->contains($color)) {
            return;
        }

        $this->colors[] = $color;
        // set the *owning* side!
        $color->setPlace($this);

        return $this;
    }

    /**
     * Remove color
     *
     * @param \App\Entity\Color $color
     */
    public function removeColor(\App\Entity\Color $color)
    {
        $this->colors->removeElement($color);
    }

    /**
     * Get colors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getColors()
    {
        return $this->colors;
    }

    public function getMainColor()
    {
        foreach ($this->colors as $color) {
            if ($color->getName() == 'color_1') {
                return $color->getCode();
            }
        }
        if (empty($this->colors[0])) {
            return '#6cb7b2';
        }
        return $this->colors[0]->getCode();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     *
     * @return self
     */
    public function setTimestamp(\App\Entity\Timestamp $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
