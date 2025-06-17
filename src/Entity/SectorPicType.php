<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectorPicTypeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class SectorPicType
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
     * @ORM\OneToMany(targetEntity="SectorPic", mappedBy="sectorPicType")
     */
    private $sectorPics;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sectorPics = new ArrayCollection();
    }

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
     * Add sectorPic
     *
     * @param \App\Entity\SectorPic $sectorPic
     *
     */
    public function addSectorPic(\App\Entity\SectorPic $sectorPic)
    {
        if ($this->sectorPics->contains($sectorPic)) {
            return;
        }

        $this->sectorPics[] = $sectorPic;

        return $this;
    }

    /**
     * Remove sectorPic
     *
     * @param \App\Entity\SectorPic $sectorPic
     */
    public function removeSectorPic(\App\Entity\SectorPic $sectorPic)
    {
        $this->sectorPics->removeElement($sectorPic);
    }

    /**
     * Get sectorPics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectorPics()
    {
        return $this->sectorPics;
    }
}
