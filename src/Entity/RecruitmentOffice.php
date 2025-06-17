<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\RecruitmentOfficeRepository")
 */
class RecruitmentOffice
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
     * @ORM\ManyToMany(targetEntity="Event",inversedBy="recruitmentOffices",cascade={"persist"})
     * @ORM\JoinTable("events_recruitment_office")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RhUser", mappedBy="recruitmentOffice", cascade={"persist"}, orphanRemoval=true)
     */
    private $rhs;

    /**
     * Constructor
     */
    public function __construct()
    {       
        $this->events = new ArrayCollection();
        $this->rhs = new ArrayCollection();
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
    * Add event
    *
    * @param \App\Entity\EventJobs $event
    *
    * @return \App\Entity\RecruitmentOffice
    */
    public function addEvent(\App\Entity\EventJobs $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
    * Remove event
    *
    * @param \App\Entity\Event $event
    */
    public function removeEvent(\App\Entity\EventJobs $event)
    {
        $this->events->removeElement($event);
    }

    /**
    * Get events
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getEvents()
    {
        return $this->events;
    }

    /**
    * Add rh
    *
    * @param \App\Entity\Rh $rh
    *
    * @return \App\Entity\User
    */
    public function addRh(\App\Entity\RhUser $rh)
    {
        $this->rhs[] = $rh;
        $rh->setRecruitmentOffice($this);

        return $this;
    }

    /**
    * Remove rh
    *
    * @param \App\Entity\Rh $rh
    */
    public function removeRh(\App\Entity\RhUser $rh)
    {
        $this->rhs->removeElement($rh);
        $rh->setRecruitmentOffice(null);
    }

    /**
    * Get rhs
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getRhs()
    {
        return $this->rhs;
    }
}
