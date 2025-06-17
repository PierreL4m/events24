<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PartnerTypeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class PartnerType
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
     * @ORM\OneToMany(targetEntity="Partner", mappedBy="partnerType")
     */
    private $partners;

    /**
     * Constructor
     */
    public function __construct()
    {       
        $this->partners = new ArrayCollection();       
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
     * Add partner
     *
     * @param \App\Entity\Partner $partner
     *
     * @return Place
     */
    public function addPartner(\App\Entity\Partner $partner)
    {
        if ($this->partners->contains($partner)) {
            return;
        }

        $this->partners[] = $partner;
        // set the *owning* side!
        $partner->setPlace($this);

        return $this;
    }

    /**
     * Remove partner
     *
     * @param \App\Entity\Partner $partner
     */
    public function removePartner(\App\Entity\Partner $partner)
    {
        $this->partners->removeElement($partner);
    }

    /**
     * Get partners
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPartners()
    {
        return $this->partners;
    }
}
