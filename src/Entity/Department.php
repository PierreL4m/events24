<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Department
 *
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="App\Repository\DepartmentRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_departement"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_departement"}}
 *           }
 *     }
 * )
 */
class Department
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_departement"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=5)
     * @Groups({"read:collection_departement"})
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_named_cities"})
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     * @Groups({"read:collection_departement"})
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_named_cities"})
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="department_order", type="integer")
     * @Groups({"read:collection_departement"})
     */
    private $departmentOrder;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="departments")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_departement"})
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="department")
     */
    private $cities;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="departments")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_departement"})
     */
    private $area;

    public function __construct()
    {        
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
     * Set number
     *
     * @param string $number
     *
     * @return Department
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Department
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
     * Set departmentOrder
     *
     * @param integer $departmentOrder
     *
     * @return Department
     */
    public function setDepartmentOrder($departmentOrder)
    {
        $this->departmentOrder = $departmentOrder;

        return $this;
    }

    /**
     * Get departmentOrder
     *
     * @return int
     */
    public function getDepartmentOrder()
    {
        return $this->departmentOrder;
    }

    /**
     * Set country
     *
     * @param \App\Entity\Country $country
     *
     * @return Department
     */
    public function setCountry(\App\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \App\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add city
     *
     * @param \App\Entity\City $city
     *
     * @return Department
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

    /**
     * Set area
     *
     * @param \App\Entity\Area $area
     *
     * @return Department
     */
    public function setArea(\App\Entity\Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \App\Entity\Area
     */
    public function getArea()
    {
        return $this->area;
    }
}
