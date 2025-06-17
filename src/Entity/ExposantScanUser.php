<?php

namespace App\Entity;

use App\Entity\Organization;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExposantScanUserRepository")
 */
class ExposantScanUser extends ClientUser
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     */
    private $savedPlainPassword;

     /**
     * @ORM\OneToOne(targetEntity="Organization", inversedBy="exposantScanUser",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $organization;


    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     *
     * @return self
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
       // $organization->setExposantScanUser($this); //Invalid parameter number: number of bound variables does not match number of tokens (Doctrine\DBAL\Exception\DriverException)

        return $this;
    }

    /**
     * @return string
     */
    public function getSavedPlainPassword()
    {
        return $this->savedPlainPassword;
    }

    /**
     * @param string $savedPlainPassword
     *
     * @return self
     */
    public function setSavedPlainPassword($savedPlainPassword)
    {
        $this->savedPlainPassword = $savedPlainPassword;

        return $this;
    }
}
