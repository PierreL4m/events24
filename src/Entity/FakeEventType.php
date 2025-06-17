<?php

namespace App\Entity;


use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FakeEventType
 */
class FakeEventType
{
    private $id;

    /**
     * @var string
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     *
     */
    private $fullName;
    
    /**
     * @var string
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     *
     */
    private $name;

    /**
     * @var string
     * @Groups({"read:incoming_events"})
     * @Groups({"read:event_exposant"})
     *
     */
    private $shortName;
    
    /**
     * @var string
     *
     */
    private $analyticsId;

    /**
     * @var bool
     *
     */
    private $mandatoryRegistration;
    
    /**
     */
    private $registrationValidation;
    
    /**
     */
    private $registrationType;
    
    /**
     * @var bool
     */
    private $displayParticipationContactInfo;
    
    /**
     * @var bool
     */
    private $recruitmentOfficeAllowed;
    
    /**
     */
    private $registrationJoblinks;

    /**
     * Constructor
     */
    public function __construct($city) 
    {
        $this->shortName = $this->name = '24';
        $this->fullName = '24h pour l\'emploi et la formation';
        if($city == 'Lomme') {
            $this->shortName = $this->name = '48';
            $this->fullName = '48h pour l\'emploi et la formation';
        }
        elseif($city == 'Lille' || $city == 'Reims') {
            $this->shortName = $this->name = 'experts';
            $this->fullName = 'RDV Recrutement Experts';
        }
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
     * Set name
     *
     * @param string $name
     *
     * @return EventType
     */
    public function setName($fullName)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
