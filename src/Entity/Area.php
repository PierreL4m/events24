<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="App\Repository\AreaRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_area"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_area"}}
 *           }
 *     }
 * )
 */
class Area
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_area"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="area_order", type="integer")
     * @Groups({"read:collection_area"})
     */
    private $areaOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     * @Groups({"read:collection_area"})
     * @Groups({"read:collection_departement"})
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_named_cities"})
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="areas")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_area"})
     * @Groups({"read:collection_departement"})
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="area")
     */
    private $cities;

    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="area")
     */
    private $departments;

    public function __construct()
    {        
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
     * Set areaOrder
     *
     * @param integer $areaOrder
     *
     * @return Area
     */
    public function setAreaOrder($areaOrder)
    {
        $this->areaOrder = $areaOrder;

        return $this;
    }

    /**
     * Get areaOrder
     *
     * @return int
     */
    public function getAreaOrder()
    {
        return $this->areaOrder;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Area
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
     * Set country
     *
     * @param \App\Entity\Country $country
     *
     * @return Area
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
     * @return Area
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
     * Add department
     *
     * @param \App\Entity\Department $department
     *
     * @return Area
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
}
