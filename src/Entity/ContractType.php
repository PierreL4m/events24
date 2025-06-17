<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContractTypeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class ContractType
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
     * @ORM\Column(type="string", length=255, unique=true)
    * @Groups({"read:get_jobs_participation"})
    * @Groups({"read:get_job_participation"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var \Array
     * 
     * @ORM\ManyToMany(targetEntity="ParticipationCompanySimple", mappedBy="contractTypes", cascade={"persist"})
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity="Job",mappedBy="contractType")
     * @ORM\JoinTable()
     */
    private $jobs;
    
    public function __construct()
    {    
        $this->participations = new ArrayCollection();
        $this->jobs = new ArrayCollection(); 
    }   

    public function __toString()
    {
        return $this->name;
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
    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
    }

    /**
    * Add participation
    *
    * @param \App\Entity\Participation $participation
    *
    * @return ParticipationType
    */
    public function addParticipation(\App\Entity\ParticipationCompanySimple $participation)
    {
        if (!$participation->contains($this)) {
            $participation->addContractType($this);
            $this->participations[] = $participation;
        }        

        return $this;
    }

    /**
    * Remove participation
    *
    * @param \App\Entity\Participation $participation
    */
    public function removeParticipation(\App\Entity\ParticipationCompanySimple $participation)
    {
        $this->participations->removeElement($participation);
        $participation->removeContractType($this);
    }

    /**
    * Get participations
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getParticipations()
    {
        return $this->participations;
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
