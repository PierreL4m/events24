<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectorRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path" = "/sector",
 *              "normalization_context"={"groups"={"read:collection_sector"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_sector"}}
 *           }
 *     }
 * )
 */
class Sector
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_sector"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_user"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_sector"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:get_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:collection_user"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_sector"})
     */
    private $slug;

     /**
     * @ORM\ManyToMany(targetEntity="Organization",inversedBy="sectors")
     * @ORM\JoinTable()
     */
    private $organizations;

     /**
     * @ORM\ManyToMany(targetEntity="Job",inversedBy="sectors")
     * @ORM\JoinTable()
     */
    private $jobs;

     /**
     * @ORM\ManyToMany(targetEntity="EventJobs",inversedBy="sectors")
     * @ORM\JoinTable()
     */
    private $events;

      /**
     * @ORM\ManyToMany(targetEntity="CandidateUser",inversedBy="sectors")
     * @ORM\JoinTable()
     */
    private $candidates;

    public function __construct()
    {
    	$this->organizations = new ArrayCollection();
    	$this->jobs = new ArrayCollection();
    	$this->events = new ArrayCollection();
    	$this->candidates = new ArrayCollection();
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
    public function getOrganizations()
    {
        return $this->organizations;
    }
    /**
     * Add organization
     *
     * @param \App\Entity\Organization $organization
     *
     * @return Event
     */
    public function addOrganization(\App\Entity\Organization $organization)
    {
        $this->organizations[] = $organization;
       
        return $this;
    }

    /**
     * Remove organization
     *
     * @param \App\Entity\Organization $organization
     */
    public function removeOrganization(\App\Entity\Organization $organization)
    {
        $this->organizations->removeElement($organization);
    }

     /**
     * @return mixed
     */
    public function getJobs()
    {
        return $this->jobs;
    }
    /**
     * Add job
     *
     * @param \App\Entity\Job $job
     *
     * @return Event
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
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }
    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return Event
     */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;
       
        return $this;
    }

    /**
     * Remove event
     *
     * @param \App\Entity\Event $event
     */
    public function removeEvent(\App\Entity\Event $event)
    {
        $this->events->removeElement($event);
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
     * @param \App\Entity\CandidateUserUser $candidate
     *
     * @return Candidate
     */
    public function addCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates[] = $candidate;
       
        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\CandidateUserUser $candidate
     */
    public function removeCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates->removeElement($candidate);
    }
}
