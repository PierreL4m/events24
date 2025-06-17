<?php

namespace App\Entity;

use App\Entity\ExposantScanUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 */
class Organization
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:get_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_jobs_participation"})
     
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:get_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_jobs_participation"})
     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $internalName;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:get_participation"})
     
     */
    private $slug;

     /**
     * @ORM\ManyToOne(targetEntity="Timestamp", inversedBy="organizations", cascade={"all"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $timestamp;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="organization", cascade={"persist"})
     */
    private $participations;

    /**
     * @ORM\OneToOne(targetEntity="ExposantScanUser", mappedBy="organization",cascade={"persist"})
     */
    private $exposantScanUser;



//////////////////////
/// many to many /////
//////////////////////
    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="OrganizationType", mappedBy="organizations", cascade={"persist"})
     */
    private $organizationTypes;

     /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Sector", mappedBy="organizations", cascade={"persist"})
     */
    private $sectors;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->organizationTypes = new ArrayCollection();
        $this->sectors = new ArrayCollection();
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
     * @return mixed
     */
    public function getParticipations()
    {
        return $this->participations;
    }
    /**
     * Add participation
     *
     * @param \App\Entity\Participation $participation
     *
     * @return Event
     */
    public function addParticipation(\App\Entity\Participation $participation)
    {
        $this->participations[] = $participation;
        $participation->setOrganizer($this);

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
     * @return mixed
     */
    public function getOrganizationTypes()
    {
        return $this->organizationTypes;
    }
    /**
     * Add organizationType
     *
     * @param \App\Entity\OrganizationType $organizationType
     *
     * @return Event
     */
    public function addOrganizationType(\App\Entity\OrganizationType $organizationType)
    {
        $this->organizationTypes[] = $organizationType;
        $organizationType->addOrganization($this);

        return $this;
    }

    /**
     * Remove organizationType
     *
     * @param \App\Entity\OrganizationType $organizationType
     */
    public function removeOrganizationType(\App\Entity\OrganizationType $organizationType)
    {
        $this->organizationTypes->removeElement($organizationType);
        $organizationType->removeOrganization($this);
    }

    /**
     * @return mixed
     */
    public function getSectors()
    {
        return $this->sectors;
    }
    /**
     * Add sector
     *
     * @param \App\Entity\Sector $sector
     *
     * @return Event
     */
    public function addSector(\App\Entity\Sector $sector)
    {
        $this->sectors[] = $sector;
        $sector->addOrganization($this);

        return $this;
    }

    /**
     * Remove sector
     *
     * @param \App\Entity\Sector $sector
     */
    public function removeSector(\App\Entity\Sector $sector)
    {
        $this->sectors->removeElement($sector);
        $sector->removeOrganization($this);
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param string $internalName
     *
     * @return self
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

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

    /**
     * @param \Array $sectors
     *
     * @return self
     */
    public function setSectors($sectors)
    {
        $this->sectors = $sectors;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExposantScanUser()
    {
        return $this->exposantScanUser;
    }

    /**
     * @param mixed $exposantScanUser
     *
     * @return self
     */
    public function setExposantScanUser(ExposantScanUser $exposantScanUser)
    {
        $this->exposantScanUser = $exposantScanUser;

        return $this;
    }
}
