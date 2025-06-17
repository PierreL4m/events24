<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:get_job_participation"}},
 *              "path" = "/job/{id}"
 *           }
 *     }
 * )
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message = "Merci de renseigner l'intitulé du poste")
     * @Assert\NotBlank(message = "Merci de renseigner l'intitulé du poste")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotNull(message = "Merci de renseigner le descriptif du poste")
     * @Assert\NotBlank(message = "Merci de renseigner le descriptif du poste")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity="Participation")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     
     */
    private $participation;


    /**
     * @ORM\ManyToOne(targetEntity="Organization")
     * @Groups({"job"})
     */
    private $organization;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="job", cascade={"persist"})
     */
    private $candidates;

    /**
     * @ORM\OneToMany(targetEntity="Skill", mappedBy="job", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $skills;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Sector", mappedBy="jobs", cascade={"persist"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $sectors;

    /**
     * @var \Array
     *
     * @ORM\ManyToOne(targetEntity="ContractType", inversedBy="jobs", cascade={"persist"})
     * @Assert\NotNull(message = "Merci de choisir le type de contrat")
     * @Assert\NotBlank(message = "Merci de choisir le type de contrat")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $contractType;


    /**
     * @ORM\ManyToOne(targetEntity="JobsList", inversedBy="jobs", cascade={"persist"})
     * @Assert\NotNull(message = "Merci de choisir le type de métier")
     * @Assert\NotBlank(message = "Merci de choisir le type de métier")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     *
     */
    private $jobType;

    /**
     * @ORM\ManyToOne(targetEntity="OfferType", inversedBy="jobs", cascade={"persist"})
     * @Groups({"read:get_jobs_participation"})
     *
     */
    private $offerType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $timeContract;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="jobs")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $city;


    public function __construct()
    {
        $this->sectors = new ArrayCollection();
        $this->candidates = new ArrayCollection();
        $this->skills = new ArrayCollection();
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
        $sector->addJob($this);

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
        $sector->removeJob($this);
    }

    /**
     * @return mixed
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * @param mixed $participation
     *
     * @return self
     */
    public function setParticipation($participation)
    {
        $this->participation = $participation;

        return $this;
    }

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
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * Add contractType
     *
     * @param \App\Entity\ContractType $contractType
     *
     * @return Job
     */
    public function setContractType(\App\Entity\ContractType $contractType)
    {
        $this->contractType = $contractType;

        return $this;
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
     * @param \App\Entity\CandidateParticipation $candidate
     *
     * @return Job
     */
    public function addCandidate(\App\Entity\CandidateParticipation $candidate)
    {
        $this->candidates[] = $candidate;
        $candidate->setJob($this);

        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\CandidateParticipation $candidate
     */
    public function removeCandidate(\App\Entity\CandidateParticipation $candidate)
    {
        $this->candidates->removeElement($candidate);
        $candidate->setJob(null);
    }

    /**
     * @return mixed
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Add skill
     *
     * @param \App\Entity\Skill $skill
     *
     * @return Job
     */
    public function addSkill(\App\Entity\Skill $skill)
    {
        $this->skills[] = $skill;
        $skill->setJob($this);

        return $this;
    }

    /**
     * Remove skill
     *
     * @param \App\Entity\Skill $skill
     */
    public function removeSkill(\App\Entity\Skill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @param string $presentation
     *
     * @return self
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    // /**
    //  * @ORM\ManyToOne(targetEntity="Degree", inversedBy="jobs")
    //  
    //  *
    //  */
    // private $degree;
    //
    // /**
    //  * Set degree
    //  *
    //  * @param \App\Entity\Degree $degree
    //  *
    //  * @return Job
    //  */
    // public function setDegree(\App\Entity\Degree $degree)
    // {
    //     $this->degree = $degree;
    //
    //     return $this;
    // }
    //
    // /**
    //  * Get degree
    //  *
    //  * @return \App\Entity\Degree
    //  */
    // public function getDegree()
    // {
    //     return $this->degree;
    // }

    /**
     * Set jobtype
     *
     * @param \App\Entity\JobsList $jobtype
     *
     * @return Job
     */
    public function setJobType(\App\Entity\JobsList $jobType)
    {
        $this->jobType = $jobType;

        return $this;
    }

    /**
     * Get jobType
     *
     * @return \App\Entity\JobsList
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * Set experience
     *
     * @param \App\Entity\OfferType $offerType
     *
     * @return Job
     */
    public function setOfferType(\App\Entity\OfferType $offerType)
    {
        $this->offerType = $offerType;

        return $this;
    }

    /**
     * Get offerType
     *
     * @return \App\Entity\OfferType
     */
    public function getOfferType()
    {
        return $this->offerType;
    }

    /**
     * @return string
     */
    public function getTimeContract()
    {
        return $this->timeContract;
    }

    /**
     * @param string $timeContract
     *
     * @return self
     */
    public function setTimeContract($timeContract)
    {
        $this->timeContract = $timeContract;

        return $this;
    }


    // /**
    //  * @var string
    //  *
    //  * @ORM\Column(type="string", length=255)
    //  
    //  */
    // private $salary;
    //
    // /**
    //  * @return string
    //  */
    // public function getSalary()
    // {
    //     return $this->salary;
    // }
    //
    // /**
    //  * @param string $salary
    //  *
    //  * @return self
    //  */
    // public function setSalary($salary)
    // {
    //     $this->salary = $salary;
    //
    //     return $this;
    // }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     *
     * @return self
     */
    public function setCity(\App\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }
}
