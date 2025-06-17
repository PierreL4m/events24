<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExposantUserRepository")
 */
class ExposantUser extends ClientUser
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="organizations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $organization;

    /**
     * Set organization
     *
     * @param \App\Entity\Organization $organization
     *
     * @return User
     */
    public function setOrganization(\App\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Add organization
     *
     * @param \App\Entity\Organization $organization
     *
     * @return User
     */
    public function addOrganization(\App\Entity\Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \App\Entity\Organization
     */
    public function getorganization()
    {
        return $this->organization;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResponsableBis", mappedBy="responsable", cascade={"persist","remove"})
     */
    private $responsableBises;

    public function __construct()
    {
        parent::__construct();
        $this->responsableBises = new ArrayCollection();
    }

    public function getEmailBises()
    {
        $emails = array();

        foreach ($this->responsableBises as $bis) {
            array_push($emails, $bis->getEmail());
        }

        return $emails;
    }
     /**
     * Add responsablesBis
     *
     * @param \App\Entity\ResponsableBis $responsablesBi
     *
     * @return User
     */
    public function addResponsableBis(\App\Entity\ResponsableBis $responsablesBis)
    {
        //stupid symfony does not recognize addResponsableBis
        $this->responsableBises[] = $responsablesBis;
        $responsablesBis->setResponsable($this);

        return $this;
    }

    /**
     * Remove responsablesBi
     *
     * @param \App\Entity\ResponsableBis $responsablesBi
     */
    public function removeResponsableBis(\App\Entity\ResponsableBis $responsablesBis)
    {
        $this->responsableBises->removeElement($responsablesBis);
    }

    /**
     * Get responsablesBis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsableBises()
    {
        return $this->responsableBises;
    }
}
