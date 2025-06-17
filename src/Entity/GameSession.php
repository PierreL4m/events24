<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\GameSessionRepository")
 */
class GameSession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $start;

    /**
     * @ORM\ManyToOne(targetEntity="ParticipationJobs", inversedBy="gameSessions")
     */
    private $participation;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="gameSession", cascade={"persist"})
     */
    private $candidates;

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
        $candidate->setGameSession($this);

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
