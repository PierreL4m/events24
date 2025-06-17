<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\EventJobsRepository")
 */
class EventJobs extends Event
{
	 /**
     * @ORM\Column(type="datetime")
     */
    private $registrationLimit;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Sector", mappedBy="events", cascade={"persist"})
     *
     */
    private $sectors;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Joblink", mappedBy="events", cascade={"persist"})
     */
    private $joblinks;

    public function __construct()
    {
        parent::__construct();
        $this->sectors = new ArrayCollection();
        $this->joblinks = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getRegistrationLimit()
    {
        return $this->registrationLimit;
    }

    /**
     * @param mixed $registrationLimit
     *
     * @return self
     */
    public function setRegistrationLimit($registrationLimit)
    {
        $this->registrationLimit = $registrationLimit;

        return $this;
    }

    /**
    * Add sector
    *
    * @param \App\Entity\Sector $sector
    *
    * @return SectorType
    */
    public function addSector(\App\Entity\Sector $sector)
    {
        $this->sectors[] = $sector;
        $sector->addEvent($this);

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
        $sector->removeEvent($this);
    }

    /**
    * Get sectors
    *
    * @return \Doctrine\Common\Collections\Collection
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
     * @return SectorType
     */

    /**
    * Get joblinks
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getJoblinks()
    {
        return $this->joblinks;
    }

    /**
    * Add joblink
    *
    * @param \App\Entity\Joblink $joblink
    *
    * @return JoblinkType
    */
    public function addJoblink(\App\Entity\Joblink $joblink)
    {
        $this->joblinks[] = $joblink;

        return $this;
    }

    /**
    * Remove joblink
    *
    * @param \App\Entity\Joblink $joblink
    */
    public function removeJoblink(\App\Entity\Joblink $joblink)
    {
        $this->joblinks->removeElement($joblink);
        $joblink->removeEvent($this);
    }
}
