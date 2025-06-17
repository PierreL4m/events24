<?php

namespace App\Entity;

use App\Entity\CandidateParticipationComment;
use App\Controller\Api\GetParticipation;
use App\Controller\Api\PatchStatsPub;
use App\Controller\Api\GetJobByParticipation;
use App\Controller\Api\GetNbJobsByContractAndParticipation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Api\PostAddAccred;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 * @ORM\Table(name="participation")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"participation_jobs" = "ParticipationJobs", "participation_company_simple" = "ParticipationCompanySimple", "participation_formation_simple" = "ParticipationFormationSimple", "participation_default" = "ParticipationDefault"})
 * @Vich\Uploadable
 * @ApiResource(
 *     collectionOperations={ *
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_participation"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "method" ="GET",
 *              "path" = "/participation/{id}",
 *              "normalization_context"={"groups"={"read:get_participation"}},
 *              "controller"=GetParticipation::class
 *           },
 *           "get_nb_jobs"={
 *              "method" ="GET",
 *              "path" = "/participation/nbjobs/{id}",
 *              "normalization_context"={"groups"={"read:get_jobs"}},
 *              "controller"=GetNbJobsByContractAndParticipation::class
 *           },
 *           "patch_stat_pub"={
 *              "method" ="PATCH",
 *              "path" = "/stats/pub/{id}",
 *              "normalization_context"={"groups"={"read:get_pub_count"}},
 *              "controller"=PatchStatsPub::class
 *           },
 *           "get_jobs_participation"={
 *              "method" ="GET",
 *              "path" = "/participation/jobs/{id}",
 *              "normalization_context"={"groups"={"read:get_jobs_participation"}},
 *              "controller"=GetJobByParticipation::class
 *           },
 *          "accreditation_add"={
 *              "method" ="POST",
 *              "path" = "/accreditation/add/{id}",
 *              "controller"=PostAddAccred::class,
 *              "normalization_context"={"groups"={"read:accreditation_add"}}
 *          }
 *     }
 * )
 */
abstract class Participation
{
    protected $type_label = "Fiche de participation ";

    abstract protected function copyChild(Participation $participation);

    abstract protected function getType();


    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:premium"})
     * @Groups({"read:get_jobs"})
     * @Groups({"read:accreditation_add"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="presentation", type="text",nullable=true)
     * @Assert\NotNull(message = "Vous devez renseigner la présentation")
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_jobs"})
     */
    private $presentation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_jobs"})
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:get_participation"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column( type="text", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $addressL1;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $addressNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $addressL2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $addressL3;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotNull(message = "Vous devez renseigner votre code postal")
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotNull(message = "Vous devez renseigner votre ville")
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $contactTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $contactName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $contactFirstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $contactEmail;

    /**
     * @var integer
     *
     * @ORM\Column(type="string", length=20,nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $contactPhone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $instagram;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $viadeo;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $linkedin;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $th;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, name="diversity")
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $div;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $senior;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $jd;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $youtube;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $recall;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $batSent;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $noBat;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $fillSent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $techGuideSent;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $noTechGuide;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_participation"})
     */
    private $standNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ack_path", type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $ackPath;

    /**
     * @var string
     *
     * @ORM\Column(name="badge", type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $badge;


    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:premium"})
     */
    private $premium;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:premium"})
     */

    private $pub;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:premium"})
     */

    private $pubMobile;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="pub", fileNameProperty="pub")
     *
     * @Assert\File(
     *     maxSize = "10000k",
     *     mimeTypesMessage = "Merci de charger un fichier taille maximale 10Mo"
     * )
     *@var File
     */
    private $file;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="pubMobile", fileNameProperty="pubMobile")
     *
     * @Assert\File(
     *     maxSize = "10000k",
     *     mimeTypesMessage = "Merci de charger un fichier taille maximale 10Mo"
     * )
     *@var File
     */
    private $fileMobile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     *
     */
    private $startPub;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     *
     */
    private $batValid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read:get_participation"})
     *
     */
    private $endPub;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:get_pub_count","read:get_participation"})
     */
    private $pubCount;

//////////////////////
/// many to one //////
//////////////////////

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StandType", inversedBy="participations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $standType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="participations")
     * @ORM\JoinColumn(name="responsable_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $responsable;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="participations")
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="Street", inversedBy="participations")
     * @Groups({"read:get_participation"})
     */
    private $street;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="participations")
     * @Groups({"read:get_participation"})
     * @Groups({"read:exposant_list"})
     * @Groups({"read:get_jobs_participation"})
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Sector")
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $sector;

    /**
     * @ORM\ManyToOne(targetEntity="Timestamp", inversedBy="participations", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $timestamp;

//////////////////////
//// one to many /////
//////////////////////


    /**
     * @ORM\OneToMany(targetEntity="ParticipationSite", mappedBy="participation", cascade={"all"}, orphanRemoval=true)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity="BilanPicture", mappedBy="participation", cascade={"all"}, orphanRemoval=true)
     * @Groups({"read:exposant_list"})
     */
    private $bilanPictures;

    /**
     * @ORM\OneToMany(targetEntity="CandidateParticipationComment", mappedBy="organizationParticipation",cascade={"persist","remove"})
     */
    private $candidateComments;

//////////////////////
/// many to many /////
//////////////////////
    /**
     * @var \Array
     *
     * @ORM\ManyToMany(targetEntity="Bat", mappedBy="participations", cascade={"persist"})
     */
    private $bats;

//////////////////////
///Images /////
//////////////////////
    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:exposant_list"})
     */
     private $logoName;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_pub_count"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo_participation", fileNameProperty="logoName")
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

    public function __construct()
    {
        $this->bats = new ArrayCollection();
        $this->sites = new ArrayCollection();
        $this->bilanPictures = new ArrayCollection();
        $this->candidateComments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->companyName;
    }

    public function copy(Participation $p = null)
    {

        $array = [
            'companyName',
            'presentation',
            'info',
            'addressL1',
            'addressNumber',
            'addressL2',
            'addressL3',
            'cp',
            'city',
            'contactTitle',
            'contactName',
            'contactFirstName',
            'contactEmail',
            'contactPhone',
            'facebook',
            'instagram',
            'twitter',
            'viadeo',
            'linkedin',
            'bool' => 'th',
            'bool' => 'div',
            'bool' => 'senior',
            'bool' => 'jd',
            'youtube',
            'responsable',
            'organization'
        ];

        foreach ($array as $key => $function_name) {
            if (!is_numeric($key)) {
                $getter = 'is' . $function_name;
            } else {
                $getter = 'get' . $function_name;
            }
            $setter = 'set' . $function_name;
            $this->$setter($p->$getter());
        }
        if ($p->getLogo()) {
            $this->setLogo(clone($p->getLogo()));
        }
        $this->setStreet($p->getStreet());

        if (is_array($this->sites)) {
            foreach ($this->sites as $site) {
                $this->removeSite($site);
            }
        }

        foreach ($p->getSites() as $site) {
            $this->addSite(clone($site));
        }
    }

    public function copyAndCheckType(Participation $participation = null)
    {
        if (!$participation) {
            return;
        }
        if (!$participation instanceof $this) {
            throw new \Exception('Vous ne pouvez pas charger une participation d\'un autre type');
        }

        $this->copyChild($participation);
    }

    public function copyFull(Participation $participation)
    {
        $this->copyAndCheckType($participation);
        $this->copy($participation);
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
     * Set responsable
     *
     * @param \App\Entity\User $responsable
     *
     * @return Participation
     */
    public function setResponsable(\App\Entity\User $responsable = null)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return \App\Entity\User
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set event
     *
     * @param \App\Entity\Event $event
     *
     * @return Participation
     */
    public function setEvent(\App\Entity\Event $event = null)
    {
        $this->event = $event;

        if ($event) {
            $event->addParticipation($this);
        }

        return $this;
    }

    /**
     * Get event
     *
     * @return \App\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }


    /**
     * Add bat
     *
     * @param \App\Entity\Bat $bat
     *
     * @return BatType
     */
    public function addBat(\App\Entity\Bat $bat)
    {
        $this->bats[] = $bat;

        return $this;
    }

    /**
     * Remove bat
     *
     * @param \App\Entity\Bat $bat
     */
    public function removeBat(\App\Entity\Bat $bat)
    {
        $this->bats->removeElement($bat);
    }

    /**
     * Get bats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBats()
    {
        return $this->bats;
    }

    /**
     * Get prensentation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @param string $presentation
     *
     * @return self
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     *
     * @return self
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

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
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressL1()
    {
        return $this->addressL1;
    }

    /**
     * @param string $addressL1
     *
     * @return self
     */
    public function setAddressL1($addressL1)
    {
        $this->addressL1 = $addressL1;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAddressNumber()
    {
        return $this->addressNumber;
    }

    /**
     * @param integer $addressNumber
     *
     * @return self
     */
    public function setAddressNumber($addressNumber)
    {
        $this->addressNumber = $addressNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressL2()
    {
        return $this->addressL2;
    }

    /**
     * @param string $addressL2
     *
     * @return self
     */
    public function setAddressL2($addressL2)
    {
        $this->addressL2 = $addressL2;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressL3()
    {
        return $this->addressL3;
    }

    /**
     * @param string $addressL3
     *
     * @return self
     */
    public function setAddressL3($addressL3)
    {
        $this->addressL3 = $addressL3;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param integer $cp
     *
     * @return self
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactTitle()
    {
        return $this->contactTitle;
    }

    /**
     * @param string $contactTitle
     *
     * @return self
     */
    public function setContactTitle($contactTitle)
    {
        $this->contactTitle = $contactTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @param string $contactName
     *
     * @return self
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactFirstName()
    {
        return $this->contactFirstName;
    }

    /**
     * @param string $contactFirstName
     *
     * @return self
     */
    public function setContactFirstName($contactFirstName)
    {
        $this->contactFirstName = $contactFirstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     *
     * @return self
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return integer
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * @param integer $contactPhone
     *
     * @return self
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param string $facebook
     *
     * @return self
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @param string $instagram
     *
     * @return self
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     *
     * @return self
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * @return string
     */
    public function getViadeo()
    {
        return $this->viadeo;
    }

    /**
     * @param string $viadeo
     *
     * @return self
     */
    public function setViadeo($viadeo)
    {
        $this->viadeo = $viadeo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param string $linkedin
     *
     * @return self
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTh()
    {
        return $this->th;
    }

    /**
     * @param bool $th
     *
     * @return self
     */
    public function setTh($th)
    {
        $this->th = $th;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDiv()
    {
        return $this->div;
    }

    /**
     * @param bool $div
     *
     * @return self
     */
    public function setDiv($div)
    {
        $this->div = $div;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSenior()
    {
        return $this->senior;
    }

    /**
     * @param bool $senior
     *
     * @return self
     */
    public function setSenior($senior)
    {
        $this->senior = $senior;

        return $this;
    }

    /**
     * @return bool
     */
    public function isJd()
    {
        return $this->jd;
    }

    /**
     * @param bool $jd
     *
     * @return self
     */
    public function setJd($jd)
    {
        $this->jd = $jd;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutube()
    {
        return $this->youtube;
    }

    /**
     * @param string $youtube
     *
     * @return self
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRecall()
    {
        return $this->recall;
    }

    /**
     * @param bool $recall
     *
     * @return self
     */
    public function setRecall($recall)
    {
        $this->recall = $recall;

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
    public function getFillSent()
    {
        return $this->fillSent;
    }

    /**
     * @param mixed $fillSent
     *
     * @return self
     */
    public function setFillSent($fillSent)
    {
        $this->fillSent = $fillSent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTechGuideSent()
    {
        return $this->techGuideSent;
    }

    /**
     * @param mixed $techGuideSent
     *
     * @return self
     */
    public function setTechGuideSent($techGuideSent)
    {
        $this->techGuideSent = $techGuideSent;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStandNumber()
    {
        return $this->standNumber;
    }

    /**
     * @param integer $standTypeNumber
     *
     * @return self
     */
    public function setStandNumber($standNumber)
    {
        $this->standNumber = $standNumber;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     *
     * @return self
     */
    public function setStreet(\App\Entity\Street $street = null)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Add site
     *
     * @param \App\Entity\Site $site
     *
     * @return SiteType
     */
    public function addSite(\App\Entity\ParticipationSite $site)
    {
        // if (is_array($this->sites)){
        //     if (!$this->sites->contains($site)) {
        //         $this->sites[] = $site;
        //         $site->setParticipation($this);
        //     }
        // }

        $this->sites[] = $site;
        $site->setParticipation($this);

        return $this;
    }

    /**
     * Remove site
     *
     * @param \App\Entity\Site $site
     */
    public function removeSite(\App\Entity\ParticipationSite $site)
    {
        if ($this->sites->contains($site)) {
            $this->sites->removeElement($site);
            // set the owning side to null (unless already changed)
            if ($site->getParticipation() === $this) {
                $site->setParticipation(null);
            }
        }
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * Add bilanPicture
     *
     * @param \App\Entity\BilanPicture $bilanPicture
     *
     * @return BilanPictureType
     */
    public function addBilanPicture(\App\Entity\BilanPicture $bilanPicture)
    {
        $this->bilanPictures[] = $bilanPicture;
        $bilanPicture->setParticipation($this);

        return $this;
    }

    /**
     * Remove bilanPicture
     *
     * @param \App\Entity\BilanPicture $bilanPicture
     */
    public function removeBilanPicture(\App\Entity\BilanPicture $bilanPicture)
    {
        $this->bilanPictures->removeElement($bilanPicture);
    }

    /**
     * Get bilanPictures
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBilanPictures()
    {
        return $this->bilanPictures;
    }

    /**
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     *
     * @return self
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return mixed
     * @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @param mixed $sector
     *
     * @return self
     */
    public function setSector($sector)
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStandType()
    {
        return $this->standType;
    }

    public function getStandSize()
    {
        return $this->standType->getDimension();
    }

    /**
     * @param mixed $standType
     *
     * @return self
     */
    public function setStandType(StandType $standType = null)
    {
        $this->standType = $standType;

        return $this;
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
    public function setTimestamp(\App\Entity\Timestamp $timestamp = null)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getAckPath()
    {
        return $this->ackPath;
    }

    /**
     * @param string $ackPath
     *
     * @return self
     */
    public function setAckPath($ackPath)
    {
        $this->ackPath = $ackPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @return string
     */
    public function getBadgeSrc()
    {
        return '/badges/' . $this->badge;
    }

    /**
     * @param string $badge
     *
     * @return self
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @ORM\Column(name="facebookVisuel", type="string", length=255, nullable=true)
     */
    private $facebookVisuel;

    /**
     * @Vich\UploadableField(mapping="visuel_facebook_participation", fileNameProperty="facebookVisuel")
     * @var File
     */
    private $facebookVisuelFile;

    /**
     * Get facebookVisuel
     *
     * @return \App\Entity\Image
     */
    public function getFacebookVisuel(): ?string
    {
        return $this->facebookVisuel;
    }

    /**
     * @param string|null $facebookVisuel
     * @return $this
     */
    public function setFacebookVisuel(?string $facebookVisuel): self
    {
        $this->facebookVisuel = $facebookVisuel;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getFacebookVisuelFile(): ?File
    {
        return $this->facebookVisuelFile;
    }

    public function setFacebookVisuelFile(?string $facebookVisuelFile = null)
    {
        $this->facebookVisuelFile = $facebookVisuelFile;
    }

    /**
     * @ORM\Column(name="signatureVisuel", type="string", length=255, nullable=true)
     */
    private $signatureVisuel;

    /**
     * @Vich\UploadableField(mapping="visuel_signature_participation", fileNameProperty="signatureVisuel")
     * @var File
     */
    private $signatureVisuelFile;

    /**
     * Get signatureVisuel
     *
     * @return \App\Entity\Image
     */
    public function getSignatureVisuel(): ?string
    {
        return $this->signatureVisuel;
    }

    /**
     * @param string|null $signatureVisuel
     * @return $this
     */
    public function setSignatureVisuel(?string $signatureVisuel): self
    {
        $this->signatureVisuel = $signatureVisuel;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getSignatureVisuelFile(): ?File
    {
        return $this->signatureVisuelFile;
    }

    public function setSignatureVisuelFile(?string $signatureVisuelFile = null)
    {
        $this->signatureVisuelFile = $signatureVisuelFile;
    }

    /**
     * @ORM\Column(name="instaVisuel", type="string", length=255, nullable=true)
     */
    private $instaVisuel;

    /**
     * @Vich\UploadableField(mapping="visuel_insta_participation", fileNameProperty="instaVisuel")
     * @var File
     */
    private $instaVisuelFile;

    /**
     * Get instaVisuel
     *
     * @return \App\Entity\Image
     */
    public function getInstaVisuel(): ?string
    {
        return $this->instaVisuel;
    }

    /**
     * @param string|null $instaVisuel
     * @return $this
     */
    public function setInstaVisuel(?string $instaVisuel): self
    {
        $this->instaVisuel = $instaVisuel;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getInstaVisuelFile(): ?File
    {
        return $this->instaVisuelFile;
    }

    public function setInstaVisuelFile(?string $instaVisuelFile = null)
    {
        $this->instaVisuelFile = $instaVisuelFile;
    }

    /**
     * @ORM\Column(name="twitterVisuel", type="string", length=255, nullable=true)
     */
    private $twitterVisuel;

    /**
     * @Vich\UploadableField(mapping="visuel_twitter_participation", fileNameProperty="twitterVisuel")
     * @var File
     */
    private $twitterVisuelFile;

    /**
     * Get twitterVisuel
     *
     * @return \App\Entity\Image
     */
    public function getTwitterVisuel(): ?string
    {
        return $this->twitterVisuel;
    }

    /**
     * @param string|null $twitterVisuel
     * @return $this
     */
    public function setTwitterVisuel(?string $twitterVisuel): self
    {
        $this->twitterVisuel = $twitterVisuel;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getTwitterVisuelFile(): ?File
    {
        return $this->twitterVisuelFile;
    }

    public function setTwitterVisuelFile(?string $twitterVisuelFile = null)
    {
        $this->twitterVisuelFile = $twitterVisuelFile;
    }

    /**
     * @ORM\Column(name="linkedinVisuel", type="string", length=255, nullable=true)
     */
    private $linkedinVisuel;

    /**
     * @Vich\UploadableField(mapping="visuel_linkedin_participation", fileNameProperty="linkedinVisuel")
     * @var File
     */
    private $linkedinVisuelFile;

    /**
     * Get linkedinVisuel
     *
     * @return \App\Entity\Image
     */
    public function getLinkedinVisuel(): ?string
    {
        return $this->linkedinVisuel;
    }

    /**
     * @param string|null $linkedinVisuel
     * @return $this
     */
    public function setLinkedinVisuel(?string $linkedinVisuel): self
    {
        $this->linkedinVisuel = $linkedinVisuel;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getLinkedinVisuelFile(): ?File
    {
        return $this->linkedinVisuelFile;
    }

    public function setLinkedinVisuelFile(?string $linkedinVisuelFile = null)
    {
        $this->linkedinVisuelFile = $linkedinVisuelFile;
    }

    /**
     * @return bool
     */
    public function isNoTechGuide()
    {
        return $this->noTechGuide;
    }

    /**
     * @param bool $noTechGuide
     *
     * @return self
     */
    public function setNoTechGuide($noTechGuide)
    {
        $this->noTechGuide = $noTechGuide;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNoBat()
    {
        return $this->noBat;
    }

    /**
     * @param bool $noBat
     *
     * @return self
     */
    public function setNoBat($noBat)
    {
        $this->noBat = $noBat;

        return $this;
    }

    /**
     * Add candidateComment
     *
     * @param \App\Entity\CandidateComment $candidateComment
     *
     * @return CandidateCommentType
     */
    public function addCandidateComment(\App\Entity\CandidateParticipationComment $candidateComment)
    {
        $this->candidateComments[] = $candidateComment;

        return $this;
    }

    /**
     * Remove candidateComment
     *
     * @param \App\Entity\CandidateComment $candidateComment
     */
    public function removeCandidateComment(\App\Entity\CandidateParticipationComment $candidateComment)
    {
        if ($this->candidateComments->contains($candidateComment)) {
            $this->candidateComments->removeElement($candidateComment);
            // set the owning side to null (unless already changed)
            if ($candidateComment->getParticipation() === $this) {
                $candidateComment->setParticipation(null);
            }
        }
    }

    /**
     * Get candidateComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCandidateComments()
    {
        return $this->candidateComments;
    }

    /**
     * @return bool
     */
    public function isPremium()
    {
        return $this->premium;
    }

    /**
     * @param bool $premium
     *
     * @return self
     */
    public function setPremium($premium)
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * @return string
     */
    public function getPub()
    {
        return $this->pub;
    }

    /**
     * @param string $pub
     *
     * @return self
     */
    public function setPub($pub)
    {
        $this->pub = $pub;

        return $this;
    }

    /**
     * @return string
     */
    public function getPubMobile()
    {
        return $this->pubMobile;
    }

    /**
     * @param string $pubMobile
     *
     * @return self
     */
    public function setPubMobile($pubMobile)
    {
        $this->pubMobile = $pubMobile;

        return $this;
    }

    /**
     * Get file
     *
     * @return File|UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param File|UploadedFile $fileMobile
     *
     * @return Post
     */
    public function setFileMobile($fileMobile = null)
    {
        $this->fileMobile = $fileMobile;
    }

    /**
     * Get file
     *
     * @return File|UploadedFile
     */
    public function getFileMobile()
    {
        return $this->fileMobile;
    }

    /**
     * Set file
     *
     * @param File|UploadedFile $file
     *
     * @return Post
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getBatValid()
    {
        return $this->batValid;
    }

    /**
     * @param mixed $batValid
     *
     * @return self
     */
    public function setBatValid($batValid)
    {
        $this->batValid = $batValid;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartPub()
    {
        return $this->startPub;
    }

    /**
     * @param mixed $startPub
     *
     * @return self
     */
    public function setStartPub($startPub)
    {
        $this->startPub = $startPub;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndPub()
    {
        return $this->endPub;
    }

    /**
     * @param mixed $endPub
     *
     * @return self
     */
    public function setEndPub($endPub)
    {
        $this->endPub = $endPub;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPubCount()
    {
        return $this->pubCount;
    }

    /**
     * @param integer $pubCount
     *
     * @return self
     */
    public function setPubCount($pubCount)
    {
        $this->pubCount = $pubCount;

        return $this;
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
}
