<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Entity\EventYoutube;
use App\Entity\Image;
use App\Entity\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\GetScannedExposantApi;
use App\Controller\Api\PostRegisterToEvent;
use App\Controller\Api\PostRegistrationCandidate;
use App\Controller\Api\GetParticipatedEventExposant;
use App\Controller\Api\GetFilteredNoteExposant;
use App\Controller\Api\GetIncomingEvents;
use App\Controller\Api\GetExposantsList;
use App\Controller\Api\GetFilteredCandidate;
use App\Controller\Api\GetJoblinkByEvent;
use App\Controller\Api\GetSlotsByEvent;
use App\Controller\Api\GetSections;
use App\Controller\Api\GetJobsByEvent;
use App\Controller\Api\RegistrationFormApi;
use App\Controller\Api\PostPreregister;
use App\Controller\Api\GetAllEventsParticipations;
use App\Controller\Api\PostContact;
use App\Controller\Api\GetPictoSector;
use App\Controller\Api\GetPremiumByEvent;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\NoteFilter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Annotation as Serializer;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @ORM\Table(name="event")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"event_jobs" = "EventJobs","event_simple" = "EventSimple"})
 * @Vich\Uploadable
 * @ApiResource(
 *     collectionOperations={
 *          "get_incoming_events"={
 *              "method" ="GET",
 *              "path" = "/events",
 *              "controller"=GetIncomingEvents::class,
 *              "normalization_context"={
 *                  "groups"={"read:incomingEvents"}
 *              }
 *          }
 *     },
 *     itemOperations={
 *          "get"={
 *              "method" ="GET",
 *              "normalization_context"={
 *                  "groups"={"read:collection_events"}
 *              },
 *              "path" = "/event/{id}"
 *           },
 *          "get_scanned_exposant"={
 *              "method" ="GET",
 *              "path" = "/candidate/seen-exposant/{id}",
 *              "normalization_context"={"groups"={"read:collection_scanned"}},
 *              "controller"=GetScannedExposantApi::class
 *          },
 *          "get_picto"={
 *              "method" ="GET",
 *              "path" = "/pictoSector/{id}",
 *              "normalization_context"={"groups"={"read:get_picto"}},
 *              "controller"=GetPictoSector::class
 *          },
 *          "get_filtered_note_exposant"={
 *              "method" ="GET",
 *              "path" = "/exposant/event/{id}/candidates",
 *              "controller"=GetFilteredNoteExposant::class,
 *              "normalization_context"={"groups"={"read:filtered_note"}}
 *          },
 *          "get_exposant_list"={
 *              "method" ="GET",
 *              "path" = "/participations/event/{id}",
 *              "controller"=GetExposantsList::class,
 *              "normalization_context"={"groups"={"read:exposant_list"}}
 *          },
 *          "get_all_events_participations"={
 *              "method" ="GET",
 *              "path" = "/participations/all/{id}",
 *              "controller"=GetAllEventsParticipations::class,
 *              "normalization_context"={"groups"={"read:exposant_list"}}
 *          },
 *          "get_filtered_candidate"={
 *              "method" ="GET",
 *              "path" = "/admin/show-candidates/{id}",
 *              "controller"=GetFilteredCandidate::class,
 *              "normalization_context"={ 
 *              "groups"={"read:filtered_candidate"}}
 *          },
 *          "get_event_exposant"={
 *              "method" ="GET",
 *              "path" = "/exposant/event",
 *              "controller"=GetParticipatedEventExposant::class,
 *              "normalization_context"={
 *                  "groups"={"read:event_exposant"}},
 *              "read"=false
 *          },
 *          "get_joblink_event"={
 *              "method" ="GET",
 *              "path" = "/joblinks/event/{id}",
 *              "controller"=GetJoblinkByEvent::class,
 *              "normalization_context"={"groups"={"read:joblink_event"}},
 *              "read"=false
 *          },
 *          "get_slots_event"={
 *              "method" ="GET",
 *              "path" = "/slots/event/{id}",
 *              "controller"=GetSlotsByEvent::class,
 *              "normalization_context"={"groups"={"read:slots_events"}},
 *              "read"=false
 *          },
 *          "get_sections"={
 *              "method" ="GET",
 *              "path" = "/sections/{id}",
 *              "controller"=GetSections::class,
 *              "normalization_context"={"groups"={"read:get_concept_section"}},
 *              "read"=false
 *          },
 *          "get_jobs_event"={
 *              "method" ="GET",
 *              "path" = "/event/jobs/{id}",
 *              "controller"=GetJobsByEvent::class,
 *              "normalization_context"={"groups"={"read:get_jobs_participation"}},
 *              "read"=false
 *          },
 *          "get_registration_form"={
 *              "method" ="GET",
 *              "path" = "/event/{id}/form",
 *              "controller"=RegistrationFormApi::class,
 *              "normalization_context"={"groups"={"read:registration_form"}},
 *              "read"=false
 *          },
 *          "preregistration"={
 *              "method" ="POST",
 *              "path" = "/event/{id}/preregistration",
 *              "controller"=PostPreregister::class,
 *              "normalization_context"={"groups"={"read:preregistration"}}
 *          },
 *          "contact"={
 *              "method" ="POST",
 *              "path" = "/event/{id}/contact",
 *              "controller"=PostContact::class,
 *              "normalization_context"={"groups"={"read:contact"}}
 *          },
 *          "registration_event_candidate"={
 *              "method" ="POST",
 *              "path" = "/event/{id}/participation",
 *              "controller"=PostRegisterToEvent::class,
 *              "normalization_context"={"groups"={"read:register_event"}}
 *          },
 *          "get_premium_event"={
 *              "method" ="GET",
 *              "path" = "/participations/premium/event/{id}",
 *              "controller"=GetPremiumByEvent::class,
 *              "normalization_context"={"groups"={"read:premium"}}
 *          }
 *     }
 * )
 */
abstract class Event implements \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:participation"})
     * @Groups({"read:premium"})
     * @Groups({"read:get_picto"})
     * @Groups({"read:collection_user"})
     * @Groups({"read:register_event"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Groups("event")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("event")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:collection_user"})
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:collection_user"})
     *
     */
    private $closingAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     *
     */
    private $online;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     *
     */
    private $offline;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:collection_events"})
     *
     */
    private $l4mRegistration;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:collection_slots"})
     * @Groups({"read:incomingEvents"})
     *
     */
    protected $has_slots;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:collection_slots"})
     *
     */
    private $is_cancel;

    /**
     * @param bool $is_cancel
     *
     * @return self
     */
    public function setis_cancel($is_cancel)
    {
        $this->is_cancel = $is_cancel;

        return $this;
    }

    /**
     * @return bool
     */
    public function getis_cancel()
    {
        return $this->is_cancel;
    }

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     *
     */
    private $rs_ready;

    /**
     * @param bool $has_slots
     *
     * @return self
     */
    public function sethas_slots($has_slots)
    {
        $this->has_slots = $has_slots;

        return $this;
    }

    /**
     * @return bool
     */
    public function ishas_slots()
    {
        return $this->getHasSlots();
    }
    
    /**
     * @return bool
     */
    public function getHasSlots()
    {
        return $this->has_slots;
    }

    /**
     * @param bool $rs_ready
     *
     * @return self
     */
    public function setrs_ready($rs_ready)
    {
        $this->rs_ready = $rs_ready;

        return $this;
    }

    /**
     * @return bool
     */
    public function isrs_ready()
    {
        return $this->rs_ready;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $batSent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $batDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ackDate;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    private $firstRecallDate;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $secondRecallDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $specificationPath;


    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $dateGuide;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $nbStand;


    /**
     * @ORM\ManyToOne(targetEntity="SpecBase", inversedBy="events")
     * @ORM\JoinColumn(name="spec_base_id", referencedColumnName="id", nullable=false)
     */
    private $specBase;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\KeyNumbers", cascade={"persist", "remove"})
     * @Groups({"read:collection_place"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:registration_form"})
     */
    private $keyNumbers;

/*********************************************/
/***************** images ********************/
/*********************************************/
    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $bannerName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $banner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Event")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:collection_user"})
     */
    private $parentEvent;

    /**
     * @Vich\UploadableField(mapping="banner_event", fileNameProperty="bannerName")
     * @var File
     */
    private $bannerFile;

    public function getBannerName()
    {
        return $this->bannerName;
    }


    public function setBannerName($bannerName)
    {
        $this->bannerName = $bannerName;

        return $this;
    }

    /**
     * Get banner
     *
     * @return \App\Entity\Image
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set banner
     *
     * @param \App\Entity\Image $banner
     *
     * @return Banner
     */
    public function setBanner(\App\Entity\Image $banner): self
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * Get bannerFile
     *
     * @return File|UploadedFile
     */
    public function getBannerFile()
    {
        return $this->bannerFile;
    }

    /**
     * Set bannerFile
     *
     * @param File|UploadedFile $bannerFile
     *
     * @return Post
     */
    public function setBannerFile($bannerFile = null)
    {
        $this->bannerFile = $bannerFile;
        if ($bannerFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $socialLogoName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $socialLogo;

    /**
     * @Vich\UploadableField(mapping="social_logo_event", fileNameProperty="socialLogoName")
     * @var File
     */
    private $socialLogoFile;

    public function getSocialLogoName()
    {
        return $this->socialLogoName;
    }


    public function setSocialLogoName($socialLogoName = null)
    {
        $this->socialLogoName = $socialLogoName;

        return $this;
    }

    /**
     * Get socialLogo
     *
     * @return \App\Entity\Image
     */
    public function getSocialLogo()
    {
        return $this->socialLogo;
    }

    /**
     * Set socialLogo
     *
     * @param \App\Entity\Image $socialLogo
     *
     * @return self
     */
    public function setSocialLogo(\App\Entity\Image $socialLogo): self
    {
        $this->socialLogo = $socialLogo;
        return $this;
    }

    /**
     * Get socialLogoFile
     *
     * @return File|UploadedFile
     */
    public function getSocialLogoFile()
    {
        return $this->socialLogoFile;
    }

    /**
     * Set socialLogoFile
     *
     * @param File|UploadedFile $socialLogoFile
     *
     * @return Post
     */
    public function setSocialLogoFile($socialLogoFile = null)
    {
        $this->socialLogoFile = $socialLogoFile;
        if ($socialLogoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $backBadgeName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $backBadge;

    /**
     * @Vich\UploadableField(mapping="back_badge_event", fileNameProperty="backBadgeName")
     * @var File
     */
    private $backBadgeFile;

    public function getBackBadgeName()
    {
        return $this->backBadgeName;
    }


    public function setBackBadgeName($backBadgeName)
    {
        $this->backBadgeName = $backBadgeName;

        return $this;
    }

    /**
     * Get backBadge
     *
     * @return \App\Entity\Image
     */
    public function getBackBadge()
    {
        return $this->backBadge;
    }

    /**
     * Set backBadge
     *
     * @param \App\Entity\Image $backBadge
     *
     * @return self
     */
    public function setBackBadge(\App\Entity\Image $backBadge): self
    {
        $this->backBadge = $backBadge;
        return $this;
    }

    /**
     * Get backbadgeFile
     *
     * @return File|UploadedFile
     */
    public function getBackBadgeFile()
    {
        return $this->backBadgeFile;
    }

    /**
     * Set backBadgeFile
     *
     * @param File|UploadedFile $backBadgeFile
     *
     * @return Post
     */
    public function setBackBadgeFile($backBadgeFile = null)
    {
        $this->backBadgeFile = $backBadgeFile;
        if ($backBadgeFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $pubAccredName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $pubAccred;

    /**
     * @Vich\UploadableField(mapping="pub_accred_event", fileNameProperty="pubAccredName")
     * @var File
     */
    private $pubAccredFile;

    public function getPubAccredName()
    {
        return $this->pubAccredName;
    }


    public function setPubAccredName($pubAccredName = null)
    {
        $this->pubAccredName = $pubAccredName;

        return $this;
    }

    /**
     * Get pubAccred
     *
     * @return \App\Entity\Image
     */
    public function getPubAccred()
    {
        return $this->pubAccred;
    }

    /**
     * Set pubAccred
     *
     * @param \App\Entity\Image $pubAccred
     *
     * @return self
     */
    public function setPubAccred(\App\Entity\Image $pubAccred) : self
    {
        $this->pubAccred = $pubAccred;
        return $this;
    }

    /**
     * Get pubAccredFile
     *
     * @return File|UploadedFile
     */
    public function getPubAccredFile()
    {
        return $this->pubAccredFile;
    }

    /**
     * Set pubAccredFile
     *
     * @param File|UploadedFile $pubAccredFile
     *
     * @return Post
     */
    public function setPubAccredFile($pubAccredFile = null)
    {
        $this->pubAccredFile = $pubAccredFile;
        if ($pubAccredFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $pubName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $pub;

    /**
     * @Vich\UploadableField(mapping="pub_event", fileNameProperty="pubName")
     * @var File
     */
    private $pubFile;

    public function getPubName()
    {
        return $this->pubName;
    }


    public function setPubName($pubName = null)
    {
        $this->pubName = $pubName;

        return $this;
    }

    /**
     * Get pub
     *
     * @return \App\Entity\Image
     */
    public function getPub()
    {
        return $this->pub;
    }

    /**
     * Set pub
     *
     * @param \App\Entity\Image $pub
     *
     * @return self
     */
    public function setPub(\App\Entity\Image $pub) : self
    {
        $this->pub = $pub;
        return $this;
    }

    /**
     * Get pubFile
     *
     * @return File|UploadedFile
     */
    public function getPubFile()
    {
        return $this->pubFile;
    }

    /**
     * Set pubFile
     *
     * @param File|UploadedFile $pubFile
     *
     * @return Post
     */
    public function setPubFile($pubFile = null)
    {
        $this->pubFile = $pubFile;
        if ($pubFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }


    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:incomingEvents"})
     */
    private $logoName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo_event", fileNameProperty="logoName")
     * @var File
     */
    private $logoFile;


    public function getLogoName()
    {
        return $this->logoName;
    }


    public function setLogoName($logoName = null)
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
     * @return self
     */
    public function setLogo(\App\Entity\Image $logo ) : self
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get logoFile
     *
     * @return File|UploadedFile
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
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:collection_events"})
     */
    private $backFacebookName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:collection_events"})
     */
    private $backFacebook;

    /**
     * @Vich\UploadableField(mapping="back_facebook", fileNameProperty="backFacebookName")
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/svg+xml", "image/gif"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg, svg, gif"
     * )
     * @var File
     */
    private $backFacebookFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:collection_events"})
     */
    private $backSignatureName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:collection_events"})
     */
    private $backSignature;

    /**
     * @Vich\UploadableField(mapping="back_signature", fileNameProperty="backSignatureName")
     * @Assert\File(
     * maxSize="1000k",
     * maxSizeMessage="Le fichier excède 1000Ko.",
     * mimeTypes={"image/png", "image/jpeg", "image/jpg", "image/svg+xml", "image/gif"},
     * mimeTypesMessage= "formats autorisés: png, jpeg, jpg, svg, gif"
     * )
     * @var File
     */
    private $backSignatureFile;

    /**
     * Get backFacebook
     *
     * @return \App\Entity\Image
     */
    public function getBackFacebook()
    {
        return $this->backFacebook;
    }

    /**
     * Set backFacebook
     *
     * @param \App\Entity\Image $backFacebook
     *
     * @return self
     */
    public function setBackFacebook(\App\Entity\Image $backFacebook = null)
    {
        $this->backFacebook = $backFacebook;
        return $this;
    }

    public function getBackFacebookName()
    {
        return $this->backFacebookName;
    }


    public function setBackfacebookName($backFacebookName)
    {
        $this->backFacebookName = $backFacebookName;

        return $this;
    }

    /**
     * Get backFacebookFile
     *
     * @return File|UploadedFile
     */
    public function getBackFacebookFile()
    {
        return $this->backFacebookFile;
    }

    /**
     * Set backFacebookFile
     *
     * @param File|UploadedFile $backFacebookFile
     *
     * @return Post
     */
    public function setBackFacebookFile($backFacebookFile = null)
    {
        $this->backFacebookFile = $backFacebookFile;
        if ($backFacebookFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * Get backSignature
     *
     * @return \App\Entity\Image
     */
    public function getbackSignature()
    {
        return $this->backSignature;
    }

    /**
     * Set backSignature
     *
     * @param \App\Entity\Image $backSignature
     *
     * @return self
     */
    public function setBackSignature(\App\Entity\Image $backSignature = null)
    {
        $this->backSignature = $backSignature;
        return $this;
    }

    public function getBackSignatureName()
    {
        return $this->backSignatureName;
    }


    public function setBackSignatureName($backSignatureName)
    {
        $this->backSignatureName = $backSignatureName;

        return $this;
    }

    /**
     * Get backSignatureFile
     *
     * @return File|UploadedFile
     */
    public function getBackSignatureFile()
    {
        return $this->backSignatureFile;
    }

    /**
     * Set backSignatureFile
     *
     * @param File|UploadedFile $backSignatureFile
     *
     * @return Post
     */
    public function setBackSignatureFile($backSignatureFile = null)
    {
        $this->backSignatureFile = $backSignatureFile;
        if ($backSignatureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return  $this;
    }

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $backInsta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $backInstaName;

    /**
     * @Vich\UploadableField(mapping="back_insta", fileNameProperty="backInstaName")
     * @var File
     */
    private $backInstaFile;

    /**
     * Get backInsta
     *
     * @return \App\Entity\Image
     */
    public function getBackInsta()
    {
        return $this->backInsta;
    }

    /**
     * Set backInsta
     *
     * @param \App\Entity\Image $backInsta
     *
     * @return self
     */
    public function setBackInsta(\App\Entity\Image $backInsta): self
    {
        $this->backInsta = $backInsta;
        return $this;
    }
    public function getBackInstaName()
    {
        return $this->backInstaName;
    }


    public function setBackInstaName($backInstaName)
    {
        $this->backInstaName = $backInstaName;

        return $this;
    }

    /**
     * Get backInstaFile
     *
     * @return File|UploadedFile
     */
    public function getBackInstaFile()
    {
        return $this->backInstaFile;
    }
    /**
     * Set backInstaFile
     *
     * @param File|UploadedFile $backInstaFile
     *
     * @return Post
     */
    public function setBackInstaFile($backInstaFile = null)
    {
        $this->backInstaFile = $backInstaFile;
        if ($backInstaFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $backTwitter;

    /**
     * @Vich\UploadableField(mapping="back_twitter", fileNameProperty="backTwitterName")
     * @var File
     */
    private $backTwitterFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $backTwitterName;

    /**
     * Get backTwitter
     *
     * @return \App\Entity\Image
     */
    public function getBackTwitter()
    {
        return $this->backTwitter;
    }

    /**
     * Set backTwitter
     *
     * @param \App\Entity\Image $backTwitter
     *
     * @return self
     */
    public function setBackTwitter(\App\Entity\Image $backTwitter): self
    {
        $this->backTwitter = $backTwitter;
        return $this;
    }

    /**
     * Get backTwitterFile
     *
     * @return File|UploadedFile
     */
    public function getBackTwitterFile()
    {
        return $this->backTwitterFile;
    }

    /**
     * Set backTwitterFile
     *
     * @param File|UploadedFile $backTwitterFile
     *
     * @return Post
     */
    public function setBackTwitterFile($backTwitterFile = null)
    {
        $this->backTwitterFile = $backTwitterFile;
        if ($backTwitterFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getBackTwitterName()
    {
        return $this->backTwitterName;
    }


    public function setBackTwitterName($backTwitterName)
    {
        $this->backTwitterName = $backTwitterName;

        return $this;
    }

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $backLinkedin;

    /**
     * @Vich\UploadableField(mapping="back_linkedin", fileNameProperty="backLinkedinName")
     * @var File
     */
    private $backLinkedinFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $backLinkedinName;


    /**
     * Get backLinkedin
     *
     * @return \App\Entity\Image
     */
    public function getBackLinkedin()
    {
        return $this->backLinkedin;
    }

    /**
     * Set backLinkedin
     *
     * @param \App\Entity\Image $backLinkedin
     *
     * @return self
     */
    public function setBackLinkedin(\App\Entity\Image $backLinkedin): self
    {
        $this->backLinkedin = $backLinkedin;
        return $this;
    }

    /**
     * Get backLinkedinFile
     *
     * @return File|UploadedFile
     */
    public function getBackLinkedinFile()
    {
        return $this->backLinkedinFile;
    }

    /**
     * Set backLinkedinFile
     *
     * @param File|UploadedFile $backLinkedinFile
     *
     * @return Post
     */
    public function setBackLinkedinFile($backLinkedinFile = null)
    {
        $this->backLinkedinFile = $backLinkedinFile;
        if ($backLinkedinFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getBackLinkedinName()
    {
        return $this->backLinkedinName;
    }


    public function setBackLinkedinName($backLinkedinName)
    {
        $this->backLinkedinName = $backLinkedinName;

        return $this;
    }

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $ogImage;

    /**
     * @Vich\UploadableField(mapping="og_image_event", fileNameProperty="ogImage")
     * @var File
     */
    private $ogImageFile;

    /**
     * Get ogImage
     *
     * @return \App\Entity\Image
     */
    public function getOgImage(): ?string
    {
        return $this->ogImage;
    }

    /**
     * Set ogImage
     *
     * @param \App\Entity\Image $ogImage
     *
     * @return self
     */
    public function setOgImage(\App\Entity\Image $ogImage): self
    {
        $this->ogImage = $ogImage;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getOgImageFile(): ?File
    {
        return $this->ogImageFile;
    }

    public function setOgImageFile(?string $ogImageFile = null)
    {
        $this->ogImageFile = $ogImageFile;
    }

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $invitation;

    /**
     * @Vich\UploadableField(mapping="invitation_event", fileNameProperty="invitation")
     * @var File
     */
    private $invitationFile;

    /**
     * Get invitation
     *
     * @return \App\Entity\Image
     */
    public function getInvitation(): ?string
    {
        return $this->invitation;
    }

    /**
     * Set invitation
     *
     * @param \App\Entity\Image $invitation
     *
     * @return Logo
     */
    public function setInvitation(\App\Entity\Image $invitation): self
    {
        $this->invitation = $invitation;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getInvitationFile(): ?File
    {
        return $this->invitationFile;
    }

    public function setInvitationFile(?string $invitationFile = null)
    {
        $this->invitationFile = $invitationFile;
    }



//////////////////////
/// many to one //////
//////////////////////
    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="events")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:participation"})
     * @Groups("event")
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:collection_user"})
     *
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="EventType", inversedBy="events", cascade={"persist"})
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id",nullable=false)
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:get_picto"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="L4MUser", inversedBy="events")
     */
    private $manager;

     /**
     * @ORM\ManyToOne(targetEntity="Timestamp", inversedBy="events", cascade={"all"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $timestamp;

//////////////////////
//// one to many /////
//////////////////////

     /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="event", orphanRemoval=true)
      * @Groups({"read:exposant_list"})
      * @Groups({"read:premium"})
     */
    private $participations;

    /**
     * @ORM\OneToMany(targetEntity="Accreditation", mappedBy="event", orphanRemoval=true)
     */
    private $accreditation;


    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="parentEvent", orphanRemoval=true)
     * @Groups({"read:collection_events"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:collection_participated_exposant"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:registration_form"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:slots_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:collection_user"})
     */
    private $childEvents;

    /**
     * @ORM\OneToMany(targetEntity="BilanFile", mappedBy="event")
     * @Groups({"read:collection_events"})
     */
    private $bilanFiles;

    /**
     * @ORM\OneToMany(targetEntity="PressFile", mappedBy="event")
     */
    private $pressFiles;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="event")
     */
    private $emails;

    /**
     * @ORM\OneToMany(targetEntity="EventYoutube", mappedBy="event")
     */
    private $youtubes;

    /**
     * @ORM\OneToMany(targetEntity="Bat", mappedBy="event")
     */
    private $bats;

     /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="event", cascade={"all"})
      * @Groups({"read:registration_form"})
      * @Groups({"read:collection_events"})
      * @Groups({"read:incomingEvents"})
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="event", cascade={"persist"})
     * @Groups({"read:filtered_candidate"})
     */
    private $candidateParticipations;

    /**
     * @ORM\OneToMany(targetEntity="Candidate", mappedBy="event", cascade={"persist"})
     */
    private $candidatesSimple;

     /**
     * @ORM\OneToMany(targetEntity="TechGuide", mappedBy="event")
     */
    private $techGuides;

//////////////////////
/// many to many /////
//////////////////////

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Partner",inversedBy="events", cascade={"persist"})
     * @ORM\JoinTable("events_partners")
     * @Groups({"read:registration_form"})
     * @Groups({"read:collection_events"})
     */
    private $partners;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="SectorPic",inversedBy="events", cascade={"persist"})
     * @ORM\JoinTable("events_sector_pic")
     */
    private $sectorPics;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="OrganizationType", mappedBy="events", cascade={"persist"})
     */
    private $organizationTypes;

    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="RecruitmentOffice", mappedBy="events", cascade={"persist"})
     */
    private $recruitmentOffices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->accreditation = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->sectorPics = new ArrayCollection();
        $this->bilanFiles = new ArrayCollection();
        $this->pressFiles = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->youtubes = new ArrayCollection();
        $this->bats = new ArrayCollection();
        $this->organizationTypes = new ArrayCollection();
        $this->candidateParticipations = new ArrayCollection();
        $this->candidatesSimple = new ArrayCollection();
        $this->techGuides = new ArrayCollection();
        $this->recruitmentOffices = new ArrayCollection();
        $this->childEvents = new ArrayCollection();
    }

    /**
     *
     * @return string|NULL
     */
    public function get48Type()
    {
        if ($this->getType()->getShortName() == '48'){
            foreach ($this->organizationTypes as $ot) {
                if ($ot->getSlug() == 'company'){
                    return 'emploi';
                }
            }
            return 'formation';
        }
        return null;
    }
    public function __toString()
    {
        $label_48 = $this->get48Type();

        if (is_string($label_48)){
            $label_48 = " - ".ucfirst($label_48);
        }

        return $this->getPlace()->getCity()." - ".$this->getDate()->format('Y').$label_48;
    }

    /**
     *
     * @return string
     */
    public function getTypeCityAndDate()
    {
        $label_48 = $this->get48Type();

        if (is_string($label_48)){
            $label_48 = " - ".ucfirst($label_48);
        }
        $place = $this->getPlace();

        return $this->getFullType().' - '.$place->getCity().$label_48." - le ".$this->getDate()->format('d/m/Y');
    }

    /**
     * Set type
     *
     * @param \App\Entity\EventType $type
     *
     * @return Place
     */
    public function setType(\App\Entity\EventType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \App\Entity\EventType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return string
     */
    public function getFullType()
    {
        return $this->getType()->getFullName();
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add participation
     *
     * @param \App\Entity\Participation $participation
     *
     * @return Event
     */
    public function addParticipation(\App\Entity\Participation $participation)
    {
        $this->participations[] = $participation;

        return $this;
    }

    /**
     * Remove participation
     *
     * @param \App\Entity\Participation $participation
     */
    public function removeParticipation(\App\Entity\Participation $participation)
    {
        $this->participations->removeElement($participation);
    }

    /**
     * Get participations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * Add participation
     *
     * @param \App\Entity\Accreditation $accreditation
     *
     * @return Event
     */
    public function addAccreditation(\App\Entity\Accreditation $accreditation)
    {
        $this->accreditation[] = $accreditation;

        return $this;
    }

    /**
     * Remove accreditation
     *
     * @param \App\Entity\Accreditation $accreditation
     */
    public function removeAccreditatiob(\App\Entity\Accreditation $accreditation)
    {
        $this->accreditation->removeElement($accreditation);
    }

    /**
     * Get accreditation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccreditation()
    {
        return $this->accreditation;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Event
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return SpecBase
     */
    public function getSpecBase()
    {
        return $this->specBase;
    }

    /**
     * @param SpecBase $event
     *
     * @return self
     */
    public function setSpecBase(SpecBase $specBase)
    {
        $this->specBase = $specBase;

        return $this;
    }

    /**
     * Set place
     *
     * @param \App\Entity\Place $place
     *
     * @return Event
     */
    public function setPlace(\App\Entity\Place $place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \App\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }


    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * @param mixed $online
     *
     * @return self
     */
    public function setOnline($online)
    {
        $this->online = $online;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOffline()
    {
        return $this->offline;
    }

    /**
     * @param mixed $offline
     *
     * @return self
     */
    public function setOffline($offline)
    {
        $this->offline = $offline;

        return $this;
    }

    /**
     * @return bool
     */
    public function isL4mRegistration()
    {
        return $this->l4mRegistration;
    }

    /**
     * @param bool $l4mRegistration
     *
     * @return self
     */
    public function setL4mRegistration($l4mRegistration)
    {
        $this->l4mRegistration = $l4mRegistration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBatSent()
    {
        return $this->batSent;
    }

    /**
     * @param mixed $batSent
     *
     * @return self
     */
    public function setBatSent($batSent)
    {
        $this->batSent = $batSent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAckDate()
    {
        return $this->ackDate;
    }

    /**
     * @param mixed $ackDate
     *
     * @return self
     */
    public function setAckDate($ackDate)
    {
        $this->ackDate = $ackDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstRecallDate()
    {
        return $this->firstRecallDate;
    }

    /**
     * @param mixed $firstRecallDate
     *
     * @return self
     */
    public function setFirstRecallDate($firstRecallDate)
    {
        $this->firstRecallDate = $firstRecallDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecondRecallDate()
    {
        return $this->secondRecallDate;
    }

    /**
     * @param mixed $secondRecallDate
     *
     * @return self
     */
    public function setSecondRecallDate($secondRecallDate)
    {
        $this->secondRecallDate = $secondRecallDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpecificationPath()
    {
        return $this->specificationPath;
    }

    /**
     * @param mixed $specificationPath
     *
     * @return self
     */
    public function setSpecificationPath($specificationPath)
    {
        $this->specificationPath = $specificationPath;

        return $this;
    }

    /**
     * Add recruitmentOffice
     *
     * @param RecruitmentOffice $recruitmentOffice
     *
     * @return RecruitmentOffice
     */
    public function addRecruitmentOffice(RecruitmentOffice $recruitmentOffice)
    {
        $this->recruitmentOffices[] = $recruitmentOffice;
        $recruitmentOffice->addEvent($this);

        return $this;
    }

    /**
     * Remove recruitmentOffice
     *
     * @param RecruitmentOffice $recruitmentOffice
     */
    public function removeRecruitmentOffice(RecruitmentOffice $recruitmentOffice)
    {
        $this->recruitmentOffices->removeElement($recruitmentOffice);
        $recruitmentOffice->removeEvent($this);
    }

    /**
     * Get RecruitmentOffices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecruitmentOffices()
    {
        return $this->recruitmentOffices;
    }

    /**
     * Add bilanFile
     *
     * @param \App\Entity\BilanFile $bilanFile
     *
     * @return Event
     */
    public function addBilanFile(\App\Entity\BilanFile $bilanFile)
    {
        $this->bilanFiles[] = $bilanFile;
        $bilanFile->setEvent($this);

        return $this;
    }

    /**
     * Remove bilanFile
     *
     * @param \App\Entity\BilanFile $bilanFile
     */
    public function removeBilanFile(\App\Entity\BilanFile $bilanFile)
    {
        $this->bilanFiles->removeElement($bilanFile);
    }

    /**
     * @return mixed
     */
    public function getBilanFiles()
    {
        return $this->bilanFiles;
    }

     /**
     * Add pressFile
     *
     * @param \App\Entity\PressFile $pressFile
     *
     * @return Event
     */
    public function addPressFile(\App\Entity\PressFile $pressFile)
    {
        $this->pressFiles[] = $pressFile;
        $pressFile->setEvent($this);

        return $this;
    }

    /**
     * Remove pressFile
     *
     * @param \App\Entity\PressFile $pressFile
     */
    public function removePressFile(\App\Entity\PressFile $pressFile)
    {
        $this->pressFiles->removeElement($pressFile);
    }

    /**
     * @return mixed
     */
    public function getPressFiles()
    {
        return $this->pressFiles;
    }

    /**
     * Add email
     *
     * @param \App\Entity\Email $email
     *
     * @return Event
     */
    public function addEmail(\App\Entity\Email $email)
    {
        $this->emails[] = $email;
        $email->setEvent($this);

        return $this;
    }

    /**
     * Remove email
     *
     * @param \App\Entity\Email $email
     */
    public function removeEmail(\App\Entity\Email $email)
    {
        $this->emails->removeElement($email);
    }

    /**
     * @return mixed
     */
    public function getEmails()
    {
        return $this->emails;
    }

     /**
     * Add youtube
     *
     * @param \App\Entity\EventYoutube $youtube
     *
     * @return Event
     */
    public function addYoutube(\App\Entity\EventYoutube $youtube)
    {
        $this->youtubes[] = $youtube;
        $youtube->setEvent($this);

        return $this;
    }

    /**
     * Remove youtube
     *
     * @param \App\Entity\EventYoutube $youtube
     */
    public function removeYoutube(\App\Entity\EventYoutube $youtube)
    {
        $this->youtubes->removeElement($youtube);
    }

    /**
     * @return mixed
     */
    public function getYoutubes()
    {
        return $this->youtubes;
    }

     /**
     * @return mixed
     */
    public function getBats()
    {
        return $this->bats;
    }

     /**
     * Add bat
     *
     * @param \App\Entity\bat $bat
     *
     * @return Event
     */
    public function addBat(\App\Entity\bat $bat)
    {
        $this->bats[] = $bat;
        $bat->setEvent($this);

        return $this;
    }

    /**
     * Remove bat
     *
     * @param \App\Entity\bat $bat
     */
    public function removebat(\App\Entity\bat $bat)
    {
        $this->bats->removeElement($bat);
    }

   /**
    * Add partner
    *
    * @param \App\Entity\Partner $partner
    *
    * @return PartnerType
    */
    public function addPartner(\App\Entity\Partner $partner)
    {
        $this->partners[] = $partner;

        return $this;
    }

    /**
     * Add sectorPic
     *
     * @param \App\Entity\SectorPic $sectorPic
     *
     * @return SectorPicType
     */
    public function addSectorPic(\App\Entity\SectorPic $sectorPic)
    {
        $this->sectorPics[] = $sectorPic;

        return $this;
    }

    /**
    * Remove partner
    *
    * @param \App\Entity\Partner $partner
    */
    public function removePartner(\App\Entity\Partner $partner)
    {
        $this->partners->removeElement($partner);
    }

    /**
    * Get partners
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getPartners()
    {
        return $this->partners;
    }



    /**
     * Remove sectorPic
     *
     * @param \App\Entity\SectorPic $sectorPic
     */
    public function removeSectorPic(\App\Entity\SectorPic $sectorPic)
    {
        $this->sectorPics->removeElement($sectorPic);
    }

    /**
     * Get sectorPics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSectorPics()
    {
        return $this->sectorPics;
    }

    /**
    * Add section
    *
    * @param \App\Entity\Section $section
    *
    * @return SectionType
    */
    public function addSection(\App\Entity\Section $section)
    {
        $this->sections[] = $section;
        $section->setEvent($this);

        return $this;
    }

    /**
    * Remove section
    *
    * @param \App\Entity\Section $section
    */
    public function removeSection(\App\Entity\Section $section)
    {
        $this->sections->removeElement($section);
    }

    /**
    * Get sections
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @return mixed
     */
    public function getClosingAt()
    {
        return $this->closingAt;
    }

    /**
     * @param mixed $closingAt
     *
     * @return self
     */
    public function setClosingAt($closingAt)
    {
        $this->closingAt = $closingAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param mixed $manager
     *
     * @return self
     */
    public function setManager(\App\Entity\L4MUser $manager)
    {
        $this->manager = $manager;
        $manager->addEvent($this);

        return $this;
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
        $organizationType->addEvent($this);

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
        $organizationType->removeEvent($this);
    }

    /**
     * @return mixed
     */
    public function getCandidateParticipations()
    {
        return $this->candidateParticipations;
    }
    /**
     * Add candidate
     *
     * @param \App\Entity\Candidate $candidate
     *
     * @return Event
     */
    public function addCandidateSimple(\App\Entity\Candidate $candidate)
    {
        $this->candidatesSimple[] = $candidate;
        $candidate->setEvent($this);

        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\Candidate $candidate
     */
    public function removeCandidateSimple(\App\Entity\Candidate $candidate)
    {
        $this->candidatesSimple->removeElement($candidate);
        $candidate->removeEvent($this);
    }

     /**
     * @return mixed
     */
    public function getCandidatesSimple()
    {
        return $this->candidatesSimple;
    }
    /**
     * Add candidateParticipation
     *
     * @param \App\Entity\CandidateParticipation $candidate
     *
     * @return Event
     */
    public function addCandidateParticipation(\App\Entity\CandidateParticipation $candidateParticipation)
    {
        $this->candidateParticipations[] = $candidateParticipation;
        $candidateParticipation->setEvent($this);

        return $this;
    }

    /**
     * Remove candidateParticipation
     *
     * @param \App\Entity\CandidateParticipation $candidateParticipation
     */
    public function removeCandidateParticipation(\App\Entity\CandidateParticipation $candidateParticipation)
    {
        $this->candidateParticipations->removeElement($candidateParticipation);
        $candidateParticipation->removeEvent($this);
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

    /**
     * @return mixed
     */
    public function getTechGuides()
    {
        return $this->techGuides;
    }
    /**
     * Add techGuide
     *
     * @param \App\Entity\TechGuide $techGuide
     *
     * @return Event
     */
    public function addTechGuide(\App\Entity\TechGuide $techGuide)
    {
        $this->techGuides[] = $techGuide;
        $techGuide->addEvent($this);

        return $this;
    }

    /**
     * Remove techGuide
     *
     * @param \App\Entity\TechGuide $techGuide
     */
    public function removeTechGuide(\App\Entity\TechGuide $techGuide)
    {
        $this->techGuides->removeElement($techGuide);
        $techGuide->removeEvent($this);
    }

    /**
     * @return mixed
     */
    public function getBatDate()
    {
        return $this->batDate;
    }

    /**
     * @param mixed $batDate
     *
     * @return self
     */
    public function setBatDate($batDate)
    {
        $this->batDate = $batDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateGuide()
    {
        return $this->dateGuide;
    }

    /**
     * @param mixed $dateGuide
     *
     * @return self
     */
    public function setDateGuide($dateGuide)
    {
        $this->dateGuide = $dateGuide;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNbStand()
    {
        return $this->nbStand;
    }

    /**
     * @param mixed $nbStand
     *
     * @return self
     */
    public function setNbStand($nbStand)
    {
        $this->nbStand = $nbStand;

        return $this;
    }

    /**
     * Get keyNumbers
     *
     * @return \App\Entity\KeyNumbers
     */
    public function getKeyNumbers()
    {
        return $this->keyNumbers;
    }

    /**
     * Set banner
     *
     * @param \App\Entity\KeyNumbers $keyNumbers
     *
     * @return self
     */
    public function setKeyNumbers(\App\Entity\KeyNumbers $keyNumbers): self
    {
        $this->keyNumbers = $keyNumbers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentEvent()
    {
        return $this->parentEvent;
    }

    /**
     * @param mixed $parentEvent
     *
     * @return \App\Entity\Event
     */
    public function setParentEvent($parentEvent)
    {
        $this->parentEvent = $parentEvent;

        return $this;
    }

    /**
     * Get childEvents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildEvents()
    {
        return $this->childEvents;
    }

    /**
     * @param integer $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function serialize()
    {
        $this->backFacebookFile = base64_encode($this->backFacebookFile);
    }

    public function unserialize($serialized)
    {
        $this->backFacebookFile = base64_decode($this->backFacebookFile);

    }
}
