<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\JoblinkSessionRepository")
 */
class JoblinkSession 
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="time", nullable = true)
     * @Groups({"read:collection_joblink"})
     */
    private $start;

    /**
     * @ORM\Column(type="time", nullable = true)
     * @Groups({"read:collection_joblink"})
     */
    private $end;

     /**
     * @ORM\ManyToOne(targetEntity="Joblink", inversedBy="joblinkSessions")
     
     */
    private $joblink;

     /**
     * @ORM\ManyToOne(targetEntity="ParticipationJobs", inversedBy="joblinkSessions")
     
     */
    private $participation;

	/**
     * @ORM\ManyToMany(targetEntity="CandidateParticipation",inversedBy="joblinkSessions")
     * @ORM\JoinTable()
     */
    private $candidates;

    public function __construct()
    {    
        $this->candidates = new ArrayCollection();   
    }

    public function __toString()
    {
        return $this->joblink->getName().' de '.$this->start->format('H:i').' à '.$this->end->format('H:i');
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     *
     * @return self
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     *
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoblink()
    {
        return $this->joblink;
    }

    /**
     * @param mixed $joblink
     *
     * @return self
     */
    public function setJoblink($joblink)
    {
        $this->joblink = $joblink;

        return $this;
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
    * Add candidate
    *
    * @param \App\Entity\CandidateParticipation $candidate
    *
    * @return CandidateType
    */
    public function addCandidate(\App\Entity\CandidateParticipation $candidate)
    {
        $this->candidates[] = $candidate;    

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
    }

    /**
    * Get candidates
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getCandidates()
    {
        return $this->candidates;
    }
}
