<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Candidate
 *
 * @ORM\Table(name="candidates_simple")
 * @ORM\Entity(repositoryClass="App\Repository\CandidateRepository")
 */
class Candidate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     *
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Assert\NotNull()
     *
     */
    private $firstname;

     /**
     * @Assert\NotNull()
     * @Assert\Email
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;


    /**
     * @var bool
     *
     * @ORM\Column(name="mailing_events", type="boolean")
     * @Assert\NotNull()
     */
    private $mailingEvents;

    /**
     * @var bool
     *
     * @ORM\Column(name="mailing_recall", type="boolean")
     * @Assert\NotNull()
     */
    private $mailingRecall;

    /**
     * @var bool
     *
     * @ORM\Column(name="phone_recall", type="boolean")
     * @Assert\NotNull()
     */
    private $phoneRecall;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length = 20)
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 10,
     *      max = 20,
     *      minMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres",
     *      maxMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres"
     * )
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */

    private $invitationPath;


    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="candidatesSimple")
     */
    private $event;


    public function __toString()
    {
        return $this->firstname." ".$this->lastname;
    }


    public function getInvitationModel()
    {
        return $this->event->getInvitationSrc();
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
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Candidate
     */
    public function setLastName($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Candidate
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }


    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     *
     * @return self
     */
    public function setEvent(\App\Entity\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMailingEvents()
    {
        return $this->mailingEvents;
    }

    /**
     * @param bool $mailingEvent
     *
     * @return self
     */
    public function setMailingEvents($mailingEvents)
    {
        $this->mailingEvents = $mailingEvents;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvitationPath()
    {
        if ($this->invitationPath){
            return '/invitations/'.$this->invitationPath;
        }
        return null;
    }

    /**
     * @param string $invitationPath
     *
     * @return self
     */
    public function setInvitationPath($invitationPath)
    {
        $this->invitationPath = $invitationPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCvPath()
    {
        return $this->cvPath;
    }

     /**
     * @return string
     */
    public function getCvPathSrc()
    {
        return 'uploads/press_files/'.$this->cvPath;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

     /**
     * @param File $file
     *
     * @return self
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

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
    /**
     * @param string $cvPath
     *
     * @return self
     */
    public function setCvPath($cvPath)
    {
        $this->cvPath = $cvPath;

        return $this;
    }


    /**
     * @return bool
     */
    public function isMailingRecall()
    {
        return $this->mailingRecall;
    }

    /**
     * @param bool $mailingRecall
     *
     * @return self
     */
    public function setMailingRecall($mailingRecall)
    {
        $this->mailingRecall = $mailingRecall;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPhoneRecall()
    {
        return $this->phoneRecall;
    }

    /**
     * @param bool $phoneRecall
     *
     * @return self
     */
    public function setPhoneRecall($phoneRecall)
    {
        $this->phoneRecall = $phoneRecall;

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
}
