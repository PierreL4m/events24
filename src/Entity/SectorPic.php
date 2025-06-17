<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectorPicRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path" = "/pictoSector",
 *              "normalization_context"={"groups"={"read:collection_pictoSector"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_pictoSector"}}
 *           }
 *     }
 * )
 */
class SectorPic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_sector"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:get_picto"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_sector"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:get_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_picto"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_sector"})
     * @Groups({"read:get_picto"})
     */
    private $slug;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="sectorPics", cascade={"persist"})
     */
    private $events;


    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return EventType
     */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;
        $event->addSectorPic($this);

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
        $event->removeSectorPic($this);
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
     * Remove all sectors
     *
     * @param \App\Entity\Sector $sector
     */
    public function removeAllSectors()
    {
        foreach ($this->sectors as $sector) {
            $this->removeSector($sector);
        }
    }
}
