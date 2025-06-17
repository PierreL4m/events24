<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * EventType
 *
 * @ORM\Entity(repositoryClass="App\Repository\EventTypeRepository")
 * @Vich\Uploadable
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_eventType"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_eventType"}}
 *           }
 *     }
 * )
 */
class EventType
{
    
    const REGISTRATION_VALIDATION_AUTO = 1;
    const REGISTRATION_VALIDATION_VIEWER = 2;
    const REGISTRATION_VALIDATION_VIEWER_RH = 3;
    
    const REGISTRATION_TYPE_STANDARD = 1;
    const REGISTRATION_TYPE_EXTENDED = 2;
    const REGISTRATION_TYPE_JOB = 3;
    const REGISTRATION_TYPE_PARTICIPATION = 4;

    const REGISTRATION_USE_JOBLINKS = 1;
    const REGISTRATION_DONT_USE_JOBLINKS = 2;
    
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255, unique=true)
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     */
    private $shortName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="analytics_id", type="string", length=255)
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     */
    private $analyticsId;

    /**
     * @var bool
     *
     * @ORM\Column(name="mandatory_registration", type="boolean", nullable=true)
     */
    private $mandatoryRegistration;
    
    /**
     * @ORM\Column(name="registration_validation", type="smallint")
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     */
    private $registrationValidation;
    
    /**
     * @ORM\Column(name="registration_type", type="smallint")
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     */
    private $registrationType;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="display_participation_contact_info", type="boolean")
     */
    private $displayParticipationContactInfo;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="recruitment_office_allowed", type="boolean")
     */
    private $recruitmentOfficeAllowed;
    
    /**
     * @ORM\ManyToOne(targetEntity="Timestamp", inversedBy="eventTypes", cascade={"all"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $timestamp;
    
     /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="type")
     */
    private $events;

     /**
     * @ORM\OneToMany(targetEntity="EventTypeParticipationType", mappedBy="eventType", cascade={"persist"})
     */
    private $participationTypes;

    /**
     * @var \Array
     * 
     * @ORM\ManyToMany(targetEntity="OrganizationType", mappedBy="eventTypes", cascade={"persist"})
     */
    private $organizationTypes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Host", inversedBy="eventTypes")
     */
    private $hosts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Host",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $host;

    /**
     * @ORM\Column(name="registration_joblinks", type="smallint")
     * @Groups({"read:collection_eventType"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     */
    private $registrationJoblinks;
    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $headerName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     */
    private $header;

    /**
     * @Vich\UploadableField(mapping="header_event_type", fileNameProperty="headerName")
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/svg+xml", "image/gif"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg, svg, gif"
     * )
     * @var File
     */
    private $headerFile;

    /**
     * Get header
     *
     * @return \App\Entity\Image
     */
    public function getHeader(): ?string
    {
        return $this->header;
    }

    /**
     * Set header
     *
     * @param \App\Entity\Image $header
     *
     * @return self
     */
    public function setHeader(\App\Entity\Image $header = null)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Get headerFile
     *
     * @return File|UploadedFile
     */
    public function getHeaderFile()
    {
        return $this->headerFile;
    }

    /**
     * Set headerFile
     *
     * @param File|UploadedFile $headerFile
     *
     * @return Post
     */
    public function setHeaderFile($headerFile = null)
    {
        $this->headerFile = $headerFile;
        if ($headerFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }
    public function getHeaderName()
    {
        return $this->headerName;
    }


    public function setHeaderName($headerName = null)
    {
        $this->headerName = $headerName;

        return $this;
    }


    /**
     * Constructor
     */
    public function __construct()
    {       
        $this->participationTypes = new ArrayCollection();   
        $this->organizationTypes = new ArrayCollection();
        $this->hosts = new ArrayCollection(); 
        $this->events = new ArrayCollection();
    }   

    public function __toString()
    {
        return $this->shortName;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return EventType
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set shortname
     *
     * @param string $shortname
     *
     * @return EventType
     */
    public function setShortName($shortname)
    {
        $this->shortName = $shortname;

        return $this;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }
    
    /**
     * Set analyticsId
     *
     * @param string $analyticsId
     *
     * @return EventType
     */
    public function setAnalyticsId($analyticsId)
    {
        $this->analyticsId = $analyticsId;
        
        return $this;
    }
    
    /**
     * Get analyticsId
     *
     * @return string
     */
    public function getAnalyticsId()
    {
        return $this->analyticsId;
    }

    /**
     * Set mandatoryRegistration
     *
     * @param boolean $mandatoryRegistration
     *
     * @return EventType
     */
    public function setMandatoryRegistration($mandatoryRegistration)
    {
        $this->mandatoryRegistration = $mandatoryRegistration;

        return $this;
    }

    /**
     * Get mandatoryRegistration
     *
     * @return bool
     */
    public function getMandatoryRegistration()
    {
        return $this->mandatoryRegistration;
    }

    /**
     * Set displayParticipationContactInfo
     *
     * @param boolean $displayParticipationContactInfo
     *
     * @return EventType
     */
    public function setDisplayParticipationContactInfo($displayParticipationContactInfo)
    {
        $this->displayParticipationContactInfo = $displayParticipationContactInfo;
        
        return $this;
    }
    
    /**
     * Get displayParticipationContactInfo
     *
     * @return bool
     */
    public function getDisplayParticipationContactInfo()
    {
        return $this->displayParticipationContactInfo;
    }
    
    /**
     * Set recruitmentOfficeAllowed
     *
     * @param boolean $recruitmentOfficeAllowed
     *
     * @return EventType
     */
    public function setRecruitmentOfficeAllowed($recruitmentOfficeAllowed): self
    {
        $this->recruitmentOfficeAllowed = $recruitmentOfficeAllowed;
        
        return $this;
    }
    
    /**
     * Get recruitmentOfficeAllowed
     *
     * @return bool
     */
    public function getRecruitmentOfficeAllowed()
    {
        return $this->recruitmentOfficeAllowed;
    }
    
    /**
     * Add Event
     *
     * @param \App\Entity\Event $event
     *
     * @return EventType
     */
    public function addEvent(\App\Entity\Event $event): self
    {
        $this->events[] = $event;
        $event->setType($this);

        return $this;
    }

    /**
     * Remove Event
     *
     * @param \App\Entity\Event $event
     */
    public function removeEvent(\App\Entity\Event $event)
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
     * @return mixed
     */
    public function getParticipationTypes()
    {
        return $this->participationTypes;
    }
    /**
     * Add participationType
     *
     * @param \App\Entity\participationType $participationType
     *
     * @return Event
     */
    public function addParticipationType(\App\Entity\EventTypeParticipationType $participationType)
    {
        $this->participationTypes[] = $participationType;
        $participationType->setEventType($this);

        return $this;
    }

    /**
     * Remove participationType
     *
     * @param \App\Entity\participationType $participationType
     */
    public function removeParticipationType(\App\Entity\EventTypeParticipationType $participationType)
    {
        $this->participationTypes->removeElement($participationType);
    }

    /**
     * @return mixed
     */
    public function getOrganizationTypes()
    {
        return $this->organizationTypes;
    }
    /**
     * Add organizationType
     *
     * @param \App\Entity\OrganizationType $organizationType
     *
     * @return Event
     */
    public function addOrganizationType(\App\Entity\OrganizationType $organizationType)
    {
        $this->organizationTypes[] = $organizationType;
        $organizationType->addEventType($this);

        return $this;
    }

    /**
     * Remove organizationType
     *
     * @param \App\Entity\OrganizationType $organizationType
     */
    public function removeOrganizationType(\App\Entity\OrganizationType $organizationType)
    {
        $this->organizationTypes->removeElement($organizationType);
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     *
     * @return self
     */
    public function setTimestamp(\App\Entity\Timestamp $timestamp)
    {
        $this->timestamp = $timestamp;
        
        return $this;
    }

    public function getRegistrationValidation(): ?int
    {
        return $this->registrationValidation;
    }
    
    public function registrationValidationAuto(): bool 
    {
        return $this->getRegistrationValidation() == self::REGISTRATION_VALIDATION_AUTO;
    }

    public function setRegistrationValidation(int $registrationValidation): self
    {
        $this->registrationValidation = $registrationValidation;

        return $this;
    }
    
    public function setRegistrationType(int $registrationType): self
    {
        $this->registrationType = $registrationType;
        
        return $this;
    }
    
    public function getRegistrationType(): ?int
    {
        return $this->registrationType;
    }

    /**
     * @return Collection|Host[]
     */
    public function getHosts(): Collection
    {
        return $this->hosts;
    }

    public function addHost(Host $host): self
    {
        if (!$this->hosts->contains($host)) {
            $this->hosts[] = $host;
        }

        return $this;
    }

    public function removeHost(Host $host): self
    {
        if ($this->hosts->contains($host)) {
            $this->hosts->removeElement($host);
        }

        return $this;
    }

    public function getHost(): ?Host
    {
        return $this->host;
    }

    public function setHost(?Host $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getRegistrationJoblinks(): ?int
    {
        return $this->registrationJoblinks;
    }

    public function setRegistrationJoblinks(int $registrationJoblinks): self
    {
        $this->registrationJoblinks = $registrationJoblinks;

        return $this;
    }
}
