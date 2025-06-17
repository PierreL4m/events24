<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_country"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_country"}}
 *           }
 *     }
 * )
 */
class Country
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_country"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="country_order", type="integer")
     */
    private $countryOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     * @Groups({"read:collection_country"})
     * @Groups({"read:collection_area"})
     * @Groups({"read:collection_departement"})
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_named_cities"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="dialing_code", type="string", length=10)
     */
    private $dialingCode;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=2)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="Area", mappedBy="country")
     */
    private $areas;

    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="country")
     */
    private $departments;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="country")
     */
    private $cities;

    public function __construct()
    {
        $this->areas = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->cities = new ArrayCollection();
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
     * Set countryOrder
     *
     * @param integer $countryOrder
     *
     * @return Country
     */
    public function setCountryOrder($countryOrder)
    {
        $this->countryOrder = $countryOrder;

        return $this;
    }

    /**
     * Get countryOrder
     *
     * @return int
     */
    public function getCountryOrder()
    {
        return $this->countryOrder;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Country
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
     * Set dialingCode
     *
     * @param string $dialingCode
     *
     * @return Country
     */
    public function setDialingCode($dialingCode)
    {
        $this->dialingCode = $dialingCode;

        return $this;
    }

    /**
     * Get dialingCode
     *
     * @return string
     */
    public function getDialingCode()
    {
        return $this->dialingCode;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Country
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add area
     *
     * @param \App\Entity\Area $area
     *
     * @return Country
     */
    public function addArea(\App\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param \App\Entity\Area $area
     */
    public function removeArea(\App\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Add department
     *
     * @param \App\Entity\Department $department
     *
     * @return Country
     */
    public function addDepartment(\App\Entity\Department $department)
    {
        $this->departments[] = $department;

        return $this;
    }

    /**
     * Remove department
     *
     * @param \App\Entity\Department $department
     */
    public function removeDepartment(\App\Entity\Department $department)
    {
        $this->departments->removeElement($department);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Add city
     *
     * @param \App\Entity\City $city
     *
     * @return Country
     */
    public function addCity(\App\Entity\City $city)
    {
        $this->cities[] = $city;

        return $this;
    }

    /**
     * Remove city
     *
     * @param \App\Entity\City $city
     */
    public function removeCity(\App\Entity\City $city)
    {
        $this->cities->removeElement($city);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }
}
