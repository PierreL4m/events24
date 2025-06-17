<?php

namespace App\Entity;

use App\Controller\Api\GetFilteredCities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     collectionOperations={
 *          "get"={
 *              "path" = "/city",
 *              "normalization_context"={"groups"={"read:collection_cities"}}
 *           },
 *          "get_filtered_cities"={
 *              "method" ="GET",
 *              "path" = "/filtered/cities",
 *              "controller"=GetFilteredCities::class,
 *              "normalization_context"={"groups"={"read:get_filtered_cities"}},
 *              "read"=false
 *          },
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_city"}}
 *           }
 *     }
 * )
 */
class City
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="city_order", type="integer", nullable=true)
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     */
    private $cityOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:collection_cities"})
     * @Groups({"read:collection_user"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=5)
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $cp;

    /**
     * @ORM\OneToMany(targetEntity="Job",mappedBy="contractType")
     * @ORM\JoinTable()
     */
    private $jobs;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var int
     *
     * @ORM\Column(name="population", type="integer")
     */
    private $population;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="cities")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     * @Groups({"read:collection_city"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:get_filtered_cities"})
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity="CandidateUser", mappedBy="city", cascade={"persist"})
     */
    private $candidates;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
        $this->jobs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * Set cityOrder
     *
     * @param integer $cityOrder
     *
     * @return City
     */
    public function setCityOrder($cityOrder)
    {
        $this->cityOrder = $cityOrder;

        return $this;
    }

    /**
     * Get cityOrder
     *
     * @return int
     */
    public function getCityOrder()
    {
        return $this->cityOrder;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return City
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
     * Get city name for api
     *
     * @return string
     */
    public function getCityNameForApi()
    {
        $country_name = $this->country->getName();

        if ($country_name == 'France') {
            return $this->name;
        } else {
            return $this->name . ' (' . $country_name . ')';
        }
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return City
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return City
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return City
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set population
     *
     * @param integer $population
     *
     * @return City
     */
    public function setPopulation($population)
    {
        $this->population = $population;

        return $this;
    }

    /**
     * Get population
     *
     * @return int
     */
    public function getPopulation()
    {
        return $this->population;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return City
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set country
     *
     * @param \App\Entity\Country $country
     *
     * @return City
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
     * Set area
     *
     * @param \App\Entity\Area $area
     *
     * @return City
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

    /**
     * Set department
     *
     * @param \App\Entity\Department $department
     *
     * @return City
     */
    public function setDepartment(\App\Entity\Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \App\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return mixed
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    /**
     * Add candidate
     *
     * @param \App\Entity\CandidateUser $candidate
     *
     * @return Job
     */
    public function addCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates[] = $candidate;
        $candidate->setCity($this);

        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\CandidateUser $candidate
     */
    public function removeCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates->removeElement($candidate);
        $candidate->setCity(null);
    }

    /**
     * Add job
     *
     * @param \App\Entity\Job $job
     *
     * @return JobType
     */
    public function addJob(\App\Entity\Job $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * Remove job
     *
     * @param \App\Entity\Job $job
     */
    public function removeJob(\App\Entity\Job $job)
    {
        $this->jobs->removeElement($job);
    }

    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }
}
