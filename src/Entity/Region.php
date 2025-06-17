<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
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
    private $name;

    public function __toString()
    {
        return $this->name;
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
}
