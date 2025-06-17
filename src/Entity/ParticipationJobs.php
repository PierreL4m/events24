<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationJobsRepository")
 */
class ParticipationJobs extends Participation
{
    /**
     * @ORM\Column(type="integer")
     * @Assert\Type("integer")
     */
    private $maxJobs = 8;

    /**
     * @var \Array
     * 
     * @ORM\OneToMany(targetEntity="JoblinkSession", mappedBy="participation", cascade={"persist"})
     */
    private $joblinkSessions;

    /**
     * @var \Array
     * 
     * @ORM\OneToMany(targetEntity="GameSession", mappedBy="participation", cascade={"persist"})
     
     */
    private $gameSessions;

    /**
     * @ORM\OneToMany(targetEntity="Job", mappedBy="participation", cascade={"all"}, orphanRemoval=true)
     */
    private $jobs;

    public function __construct()
    {
    	$this->joblinkSessions = new ArrayCollection();
    	$this->gameSessions = new ArrayCollection();
    	$this->jobs = new ArrayCollection();
    }

    public function copyChild(Participation $p)
    {

    }
    public function getType()
    {
        return $this->type_label.'multi-postes';
    }
    /**
     * @return mixed
     */
    public function getMaxJobs()
    {
        return $this->maxJobs;
    }

    /**
     * @param mixed $maxJobs
     *
     * @return self
     */
    public function setMaxJobs($maxJobs)
    {
        $this->maxJobs = $maxJobs;

        return $this;
    }
    /**
    * Add joblinkSession
    *
    * @param \App\Entity\JoblinkSession $joblinkSession
    *
    * @return ParticipationJobs
    */
    public function addJoblinkSession(\App\Entity\JoblinkSession $joblinkSession)
    {
        $this->joblinkSessions[] = $joblinkSession;
        $joblinkSession->setParticipation($this);

        return $this;
    }

    /**
    * Remove joblinkSession
    *
    * @param \App\Entity\joblinkSession $joblinkSession
    */
    public function removeJoblinkSession(\App\Entity\JoblinkSession $joblinkSession)
    {
        $this->joblinkSessions->removeElement($joblinkSession);
    }

    /**
    * Get joblinkSessions
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getJoblinkSessions()
    {
        return $this->joblinkSessions;
    }

    /**
    * Add gameSession
    *
    * @param \App\Entity\GameSession $gameSession
    *
    * @return ParticipationJobs
    */
    public function addGameSession(\App\Entity\GameSession $gameSession)
    {
        $this->gameSessions[] = $gameSession;
        $gameSession->setParticipation($this);

        return $this;
    }

    /**
    * Remove gameSession
    *
    * @param \App\Entity\gameSession $gameSession
    */
    public function removeGameSession(\App\Entity\GameSession $gameSession)
    {
        $this->gameSessions->removeElement($gameSession);
    }

    /**
    * Get gameSessions
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getGameSessions()
    {
        return $this->gameSessions;
    }

     /**
    * Add job
    *
    * @param \App\Entity\Job $job
    *
    * @return ParticipationJobs
    */
    public function addJob(\App\Entity\Job $job)
    {
        $this->jobs[] = $job;
        $job->setParticipation($this);

        return $this;
    }

    /**
    * Remove job
    *
    * @param \App\Entity\job $job
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
