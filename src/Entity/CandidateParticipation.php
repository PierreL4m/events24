<?php

namespace App\Entity;


use App\Controller\Api\GetCandidateParticipations;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\PatchAdminScan;
use App\Controller\Api\PostScanExposant;
use App\Controller\Api\DeleteCandidateParticipation;



/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateParticipationRepository")
 * @ApiResource(
 *     itemOperations={
 *          "GET",
 *          "patch_candidate_participation"={
 *              "method" ="PATCH",
 *          },
 *          "delete_candidate_participation"={
 *              "method" ="DELETE",
 *              "path" = "/candidate/participation/{id}",
 *              "controller"=DeleteCandidateParticipation::class
 *          },
 *          "patch_admin_scan"={
 *              "method" ="PATCH",
 *              "path" = "/admin/scan/{id}",
 *              "controller"=PatchAdminScan::class,
 *              "normalization_context"={"groups"={"read:scanned_candidate"}}
 *          },
 *          "post_scan_exposant"={
 *              "method" ="POST",
 *              "path" = "/exposant/scan/{id}",
 *              "controller"=PostScanExposant::class,
 *              "normalization_context"={"groups"={"read:Post_scan_candidate"}}
 *          },
 *          "get_participations"={
 *              "method" ="GET",
 *              "path" = "/candidate/participations",
 *              "normalization_context"={"groups"={"read:participation"}},
 *              "controller"=GetCandidateParticipations::class,
 *              "read"=false
 *          }
 *     }
 * )
 */
class CandidateParticipation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_candidate_participation"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:participation"})
     * @Groups({"read:registration_candidate"})
     * @Groups({"read:collection_user"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"read:collection_profile"})
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:participation"})

     */

    private $invitationPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"read:collection_profile"})
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:participation"})
     * @Groups({"read:collection_user"})

     */
    private $qrCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:participation"})
     */
    private $comesFrom;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:participation"})
     *
     */
    private $scannedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_candidate_participation"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:scanned_candidate"})
     * @Groups({"read:participation"})

     *
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="text",nullable=true)
     * @Groups({"read:participation"})
     */
    private $rhComment;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="candidates")
     * @Groups({"read:collection_profile"})
     * @Groups({"read:participation"})
     * @Groups({"read:registration_candidate"})

     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="HeardFrom", inversedBy="candidates")
     * @Groups({"read:participation"})
     */
    private $heardFrom;


    /**
     * @ORM\ManyToOne(targetEntity="Partner", inversedBy="candidates")
     */
    private $partner;

    /**
     * @var Sector
     *
     * @ORM\ManyToMany(targetEntity="JoblinkSession", mappedBy="candidates", cascade={"persist"}, fetch="EAGER")
     */
    private $joblinkSessions;

    /**
     * @ORM\ManyToOne(targetEntity="GameSession", inversedBy="candidates")
     */
    private $gameSession;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="candidateParticipations",cascade={"persist"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:participation"})
     * @Groups({"read:collection_user"})
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="Job", inversedBy="candidates")
     * @ORM\JoinColumn(nullable=true,onDelete="SET NULL")
     */
    private $job;

    /**
     * @ORM\OneToMany(targetEntity="Grade", mappedBy="candidate_participation", cascade={"persist"})
     */
    private $grades;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipationComment", mappedBy="candidateParticipation", cascade={"persist","remove"})
     */
    private $candidateComments;

    /**
     * @ORM\ManyToOne(targetEntity="CandidateUser", inversedBy="candidateParticipations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"read:collection_note_exposant"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:filtered_candidate"})
     * @Groups({"read:Post_scan_candidate"})
     * @Groups({"read:participation"})
     */
    private $candidate;

    /**
     * @ORM\ManyToOne(targetEntity="Slots", inversedBy="candidateParticipations")
     * @ORM\JoinColumn(nullable=true,onDelete="SET NULL")
     * @Groups({"read:collection_register"})
     * @Groups({"read:participation"})
     */
    private $slot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $handledBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $statusDate;

    public function __construct()
    {
        $this->sectors = new ArrayCollection();
        $this->joblinkSessions = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->candidateComments = new ArrayCollection();
        $this->createdAt = new \DateTime;
        $this->statusDate = new \DateTime;
    }


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @param string $qrCode
     *
     * @return self
     */
    public function setQrCode($qrCode)
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGameSession()
    {
        return $this->gameSession;
    }

    /**
     * @param mixed $gameSession
     *
     * @return self
     */
    public function setGameSession($gameSession)
    {
        $this->gameSession = $gameSession;

        return $this;
    }

    /**
     * Add joblinkSession
     *
     * @param \App\Entity\JoblinkSession $joblinkSession
     *
     * @return JoblinkSessionType
     */
    public function addJoblinkSession(\App\Entity\JoblinkSession $joblinkSession)
    {
        $this->joblinkSessions[] = $joblinkSession;
        $joblinkSession->addCandidate($this);

        return $this;
    }

    /**
     * Remove joblinkSession
     *
     * @param \App\Entity\JoblinkSession $joblinkSession
     */
    public function removeJoblinkSession(\App\Entity\JoblinkSession $joblinkSession)
    {
        $this->joblinkSessions->removeElement($joblinkSession);
        $joblinkSession->removeCandidate($this);
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->setStatusDate(new \DateTime());

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeardFrom()
    {
        return $this->heardFrom;
    }

    /**
     * @return mixed
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param mixed $partner
     *
     * @return self
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get events
     *
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add event
     *
     * @param \App\Entity\Event $event
     *
     * @return EventType
     */
    public function setEvent(\App\Entity\Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get job
     *
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Add job
     *
     * @param \App\Entity\Job $job
     *
     * @return EventType
     */
    public function setJob(\App\Entity\Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvitationPath()
    {
        if ($this->invitationPath) {
            return '/invitations/' . $this->invitationPath;
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
     * @param mixed $heardFrom
     *
     * @return self
     */
    public function setHeardFrom($heardFrom)
    {
        $this->heardFrom = $heardFrom;

        return $this;
    }

    /**
     * Add grade
     *
     * @param \App\Entity\Grade $grade
     *
     * @return GradeType
     */
    public function addGrade(\App\Entity\Grade $grade)
    {
        $this->grades[] = $grade;
        $grade->setCandidate($this);

        return $this;
    }

    /**
     * Remove grade
     *
     * @param \App\Entity\Grade $grade
     */
    public function removeGrade(\App\Entity\Grade $grade)
    {
        $this->grades->removeElement($grade);
    }

    /**
     * Get grades
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrades()
    {
        return $this->grades;
    }


    /**
     * @return mixed
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param mixed $candidate
     *
     * @return self
     */
    public function setCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidate = $candidate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param mixed $slot
     *
     * @return self
     */
    public function setSlot(\App\Entity\Slots $slot)
    {
        $this->slot = $slot;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCandidateComments()
    {
        return $this->candidateComments;
    }

    /**
     * Add candidatecomment
     *
     * @param \App\Entity\Candidatecomment $candidatecomment
     *
     * @return CandidatecommentType
     */
    public function addCandidatecomment(\App\Entity\CandidateParticipationComment $candidatecomment)
    {
        $this->candidatecomments[] = $candidatecomment;

        return $this;
    }

    /**
     * Remove candidatecomment
     *
     * @param \App\Entity\Candidatecomment $candidatecomment
     */
    public function removeCandidateComment(\App\Entity\CandidateParticipationComment $candidatecomment)
    {
        $this->candidateComments->removeElement($candidatecomment);
    }


    /**
     * @return mixed
     */
    public function getScannedAt()
    {
        return $this->scannedAt;
    }

    /**
     * @param mixed $scannedAt
     *
     * @return self
     */
    public function setScannedAt($scannedAt)
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getComesFrom()
    {
        return $this->comesFrom;
    }

    /**
     * @param string $comesFrom
     *
     * @return self
     */
    public function setComesFrom($comesFrom)
    {
        $this->comesFrom = $comesFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getRhComment()
    {
        if ($this->rhComment) {
            return $this->rhComment;
        }

        switch ($this->status->getSlug()) {
            case 'registered' :
                return 'Merci de votre inscription. Nous allons étudier votre candidature au plus vite.';
            case 'refused':
            case 'refused_after_call':
                return 'Malgré la qualité de votre candidature, cette dernière n\'a pas été retenue.';
            case 'waiting':
                return 'Nous avons retenu votre candidature. Malheureusement le nombre de place est limité, vous avez été placé en liste d\'attente.';
        }
    }

    /**
     * @param string $rhComment
     *
     * @return self
     */
    public function setRhComment($rhComment)
    {
        $this->rhComment = $rhComment;

        return $this;
    }

    public function getHandledBy(): ?User
    {
        return $this->handledBy;
    }

    public function setHandledBy(?User $handledBy): self
    {
        $this->handledBy = $handledBy;

        return $this;
    }

    public function getStatusDate(): ?\DateTimeInterface
    {
        return $this->statusDate;
    }

    public function setStatusDate(\DateTimeInterface $statusDate): self
    {
        $this->statusDate = $statusDate;

        return $this;
    }

}
