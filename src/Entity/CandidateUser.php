<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\Api\GetCandidateProfileApi;
use App\Controller\Api\GetScannedExposantApi;
use App\Controller\Api\PatchProfileCandidate;
use App\Controller\Api\PatchWorkingCandidat;
use App\Controller\Api\PatchCvCandidat;
use App\Controller\Api\PatchMailCandidat;
use App\Controller\Api\PatchSmsCandidat;
use App\Controller\Api\PatchInfosCandidat;
use App\Controller\Api\PatchCandidateParticipation;
use App\Controller\Api\DeleteCandidateParticipation;
use App\Controller\Api\DeleteCandidateAccount;
use App\Controller\Api\PatchToken;
use App\Controller\Api\PostRegistrationCandidate;
use App\Controller\Api\PatchUserDevice;
use App\Controller\Api\PostRequestPassword;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Event;


/**
 * Candidate
 *
 * @ORM\Entity(repositoryClass="App\Repository\CandidateUserRepository")
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *           },
 *          "get_profile"={
 *              "method" ="GET",
 *              "path" = "/candidate/profile",
 *              "normalization_context"={"groups"={"read:collection_profile"}},
 *              "controller"=GetCandidateProfileApi::class,
 *              "read"=false
 *          },
 *          "patch_profile_candidate"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/profile/edit",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchProfileCandidate::class,
 *              "read"=false
 *          },
 *          "patch_candidate_participation"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/participation/edit/{id}",
 *              "normalization_context"={"groups"={"read:patch_candidate_participation"}},
 *              "controller"=PatchParticipationCandidate::class
 *          },
 *          "patch_token"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/edit-token-notifications",
 *              "controller"=PatchToken::class,
 *              "normalization_context"={"groups"={"read:patch_token"}},
 *              "read"=false
 *          },
 *          "delete_candidate_account"={
 *              "method" ="DELETE",
 *              "path" = "/candidate/remove-account",
 *              "normalization_context"={"groups"={"read:delete_candidate_account"}},
 *              "controller"=DeleteCandidateAccount::class,
 *              "read"=false
 *          },
 *          "registration_candidate"={
 *              "method" ="POST",
 *              "path" = "/event/{id}/registration",
 *              "controller"=PostRegistrationCandidate::class,
 *              "normalization_context"={
 *                  "groups"={"read:registration_candidate"}
 *              },
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          },
 *          "patch_user_device"={
 *              "method" ="PATCH",
 *              "path" = "/user/device",
 *              "controller"=PatchUserDevice::class,
 *              "read"=false,
 *              "normalization_context"={"groups"={"read:patch_user_device"}}
 *          },
 *          "request_password"={
 *              "method" ="POST",
 *              "path" = "/request/password",
 *              "normalization_context"={"groups"={"read:request_password"}},
 *              "controller"=PostRequestPassword::class,
 *              "read"=false
 *          },
 *          "working_candidate"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/working/update/{id}",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchWorkingCandidat::class,
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          },
 *          "sms_candidat"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/sms/update/{id}",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchSmsCandidat::class,
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          },
 *          "mail_candidat"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/mail/update/{id}",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchMailCandidat::class,
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          },
 *          "infos_candidate"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/infos/update/{id}",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchInfosCandidat::class,
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          },
 *          "cv_candidate"={
 *              "method" ="PATCH",
 *              "path" = "/candidate/cv/update/{id}",
 *              "normalization_context"={"groups"={"read:patch_profile_candidate"}},
 *              "controller"=PatchCvCandidat::class,
 *              "read"=false,
 *               "denormalization_context"={
 *                  "disable_type_enforcement"=true
 *              }
 *          }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"event.id"})
 */
class CandidateUser extends User
{
    public function getType()
    {
        return 'Candidat';
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="mailing_events", type="boolean",nullable=true)
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_register"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:subscribe_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     */
    private $mailingEvents;

    /**
     * @var bool
     *
     * @Groups({"read:collection_profile"})
     * @ORM\Column(name="mailing_recall", type="boolean",nullable=true)
     * @Groups({"read:collection_register"})
     * @Groups({"read:collection_edit_profil"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     */
    private $mailingRecall;

    /**
     * @var bool
     * @Groups({"read:collection_profile"})
     * @ORM\Column(name="phone_recall", type="boolean",nullable=true)
     * @Groups({"read:collection_register"})
     * @Groups({"read:collection_edit_profil"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $phoneRecall;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     */
    private $wantedJob;

    /**
     * @ORM\ManyToOne(targetEntity="Mobility", inversedBy="candidates")
     * @ Assert\NotNull(
     *     message = "Merci de saisir votre mobilité"
     * )
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $mobility;

    /**
     * @ORM\ManyToOne(targetEntity="Degree", inversedBy="candidates")
     * @ Assert\NotNull(
     *     message = "Merci de saisir votre diplôme"
     * )
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $degree;

    /**
     * @var bool
     *
     * @ORM\Column(name="working", type="boolean",nullable=true)
     * @Groups({"read:patch_profile_scanned"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     *
     */
    private $working;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:collection_user"})
     */

    private $cv;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\File(
     *     maxSize = "1000000k",
     *     mimeTypes = {"application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.oasis.opendocument.text", "application/octet-stream", "image/jpeg", "image/jpg", "image/png","application/zip"},
     *     mimeTypesMessage = "Merci de charger un fichier pdf, doc, docx, odt, taille maximale 1Go"
     * )
     * @Groups({"read:collection_register"})
     * @Groups({"read:Post_scan_candidate"})
     */
    private $file;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenNotifications;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipation", mappedBy="candidate",cascade={"persist","remove"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $candidateParticipations;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="candidates")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $city;

    /**
     * @var Sector
     *
     * @ORM\ManyToMany(targetEntity="Sector", mappedBy="candidates", cascade={"persist"})
     * @Assert\NotNull(
     *     message = "Merci de choisir au moins un secteur"
     * )
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:patch_profile_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $sectors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sector", cascade={"persist", "merge"})
     */
    private $rhSectors;


    public function __construct()
    {
        $this->candidateParticipations = new ArrayCollection();
        $this->sectors = new ArrayCollection();
        $this->rhSectors = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname . " " . $this->lastname;
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
     * Get working
     *
     * @return int
     */
    public function getWorking()
    {
        return $this->working;
    }

    /**
     * @return mixed
     */
    public function getWantedJob()
    {
        return $this->wantedJob;
    }

    /**
     * @param mixed $job
     *
     * @return self
     */
    public function setWantedJob($job)
    {
        $this->wantedJob = $job;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMobility()
    {
        // if ($this->mobility && $this->mobility->getName() == 'Ville'){
        //     if ($this->city){
        //         return $this->city;
        //     }
        // }
        return $this->mobility;

    }

    /**
     * @param mixed $mobility
     *
     * @return self
     */
    public function setMobility($mobility)
    {
        $this->mobility = $mobility;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * @param mixed $degree
     *
     * @return self
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     *
     * @return self
     */
    public function setCity(\App\Entity\City $city = null)
    {
        $this->city = $city;

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
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @return string
     */
    public function getCvPath()
    {
        if (!$this->cv) {
            return null;
        }
        return '/uploads/cvs/' . $this->cv;
    }

    /**
     * @param string $cv
     *
     * @return self
     */
    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Add candidateParticipation
     *
     * @param \App\Entity\CandidateParticipation $participation
     *
     * @return ParticipationType
     */
    public function addCandidateParticipation(\App\Entity\CandidateParticipation $participation)
    {
        $this->candidateParticipations[] = $participation;
        $participation->setCandidate($this);

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
    }

    /**
     * Get candidateParticipations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCandidateParticipations()
    {
        return $this->candidateParticipations;
    }


    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     *
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($file) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }

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
     * Add sector
     *
     * @param \App\Entity\Sector $sector
     *
     * @return SectorType
     */
    public function addSector(\App\Entity\Sector $sector)
    {
        if (!$this->sectors->contains($sector)) {
            $this->sectors[] = $sector;
            $sector->addCandidate($this);
        }

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
        $sector->removeCandidate($this);
    }

    /**
     * Remove all sectors
     *
     * @param \App\Entity\Sector $sector
     */
    public function removeAllSectors()
    {
        foreach ($this->sectors as $sector) {
            $this->removeSector($sector);
        }
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
     * @return string
     */
    public function getTokenNotifications()
    {
        return $this->tokenNotifications;
    }

    /**
     * @param string $tokenNotifications
     *
     * @return self
     */
    public function setTokenNotifications($tokenNotifications)
    {
        $this->tokenNotifications = $tokenNotifications;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWorking()
    {
        return $this->working;
    }

    /**
     * @param bool $working
     *
     * @return self
     */
    public function setWorking($working)
    {
        $this->working = $working;

        return $this;
    }

    /**
     * @return Collection|Sector[]
     */
    public function getRhSectors(): Collection
    {
        return $this->rhSectors;
    }

    public function addRhSector(Sector $rhSector): self
    {
        if (!$this->rhSectors->contains($rhSector)) {
            $this->rhSectors[] = $rhSector;
        }

        return $this;
    }

    public function removeRhSector(Sector $rhSector): self
    {
        if ($this->rhSectors->contains($rhSector)) {
            $this->rhSectors->removeElement($rhSector);
        }

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        $this->file = base64_encode($this->file);
        return [get_object_vars($this), parent::__serialize()];
    }

    /**
     * {@inheritdoc}
     */
    public function __unserialize(array $data): void
    {
        [$selfData, $parentData] = $data;
        foreach($selfData as $k => $v) {
            $this->$k = $v;
        }
        $this->file = base64_decode($this->file);
        parent::__unserialize($parentData);
    }
}
