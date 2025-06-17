<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PartnerRepository")
 * @Vich\Uploadable
 */
class Partner
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
      * @Groups({"read:incoming_events"})
      * @Groups({"read:collection_events"})
     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $baseline;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

     /**
     * @ORM\ManyToOne(targetEntity="PartnerType", inversedBy="partners")
     * @ORM\JoinColumn()
     * @Groups({"read:collection_events"})
     */
    private $partnerType;

    /**
     * @var \Array
     * 
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="partners", cascade={"persist"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="partner", cascade={"persist"})
     */
    private $candidates;


    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:collection"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     */
    private $logoName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo_partenaire", fileNameProperty="logoName")
     * @var File
     */
    private $logoFile;

    public function getLogoName()
    {
        return $this->logoName;
    }


    public function setLogoName($logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \App\Entity\Image
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param \App\Entity\Image $logo
     *
     * @return Logo
     */
    public function setLogo(\App\Entity\Image $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * Set logoFile
     *
     * @param File|UploadedFile $logoFile
     *
     * @return Post
     */
    public function setLogoFile($logoFile = null)
    {
        $this->logoFile = $logoFile;
        if ($logoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
    
    public function __construct()
    {    
        $this->events = new ArrayCollection();
        $this->candidates = new ArrayCollection();
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseline()
    {
        return $this->baseline;
    }

    /**
     * @param string $baseline
     *
     * @return self
     */
    public function setBaseline($baseline)
    {
        $this->baseline = $baseline;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
    * Add event
    *
    * @param \App\Entity\Event $event
    *
    * @return EventType
    */
    public function addEvent(\App\Entity\Event $event)
    {
        $this->events[] = $event;
        $event->addPartner($this);  

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
        $event->removePartner($this);
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
    * Add candidate
    *
    * @param \App\Entity\CandidateUser $candidate
    *
    * @return CandidateType
    */
    public function addCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates[] = $candidate;
        $candidate->setPartner($this);

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
    * Get candidates
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getCandidates()
    {
        return $this->candidates;
    }

    public function getSlugType()
    {
        if ($this->partnerType){
            return $this->partnerType->getSlug();
        }
    }
    public function getType()
    {
        if ($this->partnerType){
            return $this->partnerType->getName();
        }
    }

    /**
     * @return mixed
     */
    public function getPartnerType()
    {
        return $this->partnerType;
    }

    /**
     * @param mixed $partnerType
     *
     * @return self
     */
    public function setPartnerType($partnerType)
    {
        $this->partnerType = $partnerType;

        return $this;
    }
}
