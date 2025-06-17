<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\Api\GetSendProfile;
use App\Controller\Api\PatchNote;
use App\Controller\Api\GetNoteExposant;
use ApiPlatform\Core\Annotation\ApiResource;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidateParticipationCommentRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_user"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_comment"}}
 *           },
 *          "get_send_profile"={
 *              "method" ="GET",
 *              "path" = "/exposant/send-profile/{id}",
 *              "normalization_context"={"groups"={"read:request_password"}},
 *              "controller"=GetSendProfile::class
 *          },
 *          "patch_note"={
 *              "method" ="PATCH",
 *              "path" = "/exposant/note/{id}",
 *              "normalization_context"={"groups"={"read:patch_note"}},
 *              "controller"=PatchNote::class
 *          },
 *          "get_note_exposant"={
 *              "method" ="GET",
 *              "path" = "/exposant/note/{id}",
 *              "normalization_context"={"groups"={"read:filtered_note"}},
 *              "controller"=GetNoteExposant::class
 *          }
 *     }
 * )
 */
class CandidateParticipationComment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_comment"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:patch_note"})
     * @Groups({"read:Post_scan_candidate"})
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="do_like", type="smallint", nullable=true)
     * @Groups({"read:collection_comment"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:patch_note"})
     * @Groups({"read:Post_scan_candidate"})
     *
     */
    private $like;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:collection_comment"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:patch_note"})
     * @Groups({"read:Post_scan_candidate"})
     *
     */
    private $scannedAt;
    /**
     * @var text
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     * @Groups({"read:filtered_note"})
     * @Groups({"read:patch_note"})
     */
    private $comment;

    /**
     * @var bool
     *
     * @ORM\Column(name="favortie", type="boolean", nullable=true)
      * @Groups({"read:patch_note"})
     *
     */
    private $favorite;

    /**
     * @ORM\ManyToOne(targetEntity="CandidateParticipation", inversedBy="candidateComments")
     * @Groups({"read:collection_comment"})
     * @Groups({"read:filtered_note"})
     * @Groups({"read:Post_scan_candidate"})
     */
    private $candidateParticipation;

    /**
     * @ORM\ManyToOne(targetEntity="Participation", inversedBy="candidateComments")
     * @Groups({"read:collection_scanned"})
     */
    private $organizationParticipation;


    public function __construct()
    {
        $this->like = 0;
    }
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set like
     *
     * @param integer $like
     *
     * @return self
     */
    public function setLike($like)
    {
        $this->like = $like;

        return $this;
    }

    /**
     * Get like
     *
     * @return integer
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return CandidateParticipationNote
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getCandidateParticipation()
    {
        return $this->candidateParticipation;
    }

    /**
     * @param mixed $candidateParticipation
     *
     * @return self
     */
    public function setCandidateParticipation(\App\Entity\CandidateParticipation $candidateParticipation)
    {
        $this->candidateParticipation = $candidateParticipation;
        $candidateParticipation->addCandidateComment($this);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganizationParticipation()
    {
        return $this->organizationParticipation;
    }

    /**
     * @param mixed $organizationParticipation
     *
     * @return self
     */
    public function setOrganizationParticipation(\App\Entity\Participation $organizationParticipation)
    {
        $this->organizationParticipation = $organizationParticipation;
        $organizationParticipation->addCandidateComment($this);

        return $this;
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
     * @return bool
     */
    public function isFavorite()
    {
        return $this->favorite;
    }

    /**
     * @param bool $favorite
     *
     * @return self
     */
    public function setFavorite($favorite)
    {
        if ($favorite == false){
            $favorite = null; // this keeps sort candidate participation comment right
        }
        $this->favorite = $favorite;

        return $this;
    }
}
