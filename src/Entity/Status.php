<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class Status
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_profile"})
      * @Groups({"read:registration_candidate"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="status", cascade={"persist"})
     */
    private $candidates;

    public function __construct()
    {
    	$this->candidates = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
        $candidate->setStatus($this);
       
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
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

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
}
