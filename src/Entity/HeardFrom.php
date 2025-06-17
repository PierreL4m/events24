<?php

namespace App\Entity;

use App\Entity\CandidateParticipationComment;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\HeardFromRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class HeardFrom
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
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="heardFrom", cascade={"persist"})
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
     * @param \App\Entity\CandidateParticipationComments $candidate
     *
     * @return Job
     */
    public function addCandidate(\App\Entity\CandidateParticipationComment $candidate)
    {
        $this->candidates[] = $candidate;
        $candidate->setHeardFrom($this);
       
        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\CandidateParticipationComments $candidate
     */
    public function removeCandidate(\App\Entity\CandidateParticipationComment $candidate)
    {
        $this->candidates->removeElement($candidate);
    }
}
