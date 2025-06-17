<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JoblinkRepository")
 * @UniqueEntity(fields="slug", message="Un slug existe déjà avec ce slug.")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @Vich\Uploadable
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path"="/joblink/events",
 *              "normalization_context"={"groups"={"read:collection_joblink"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"read:collection_joblink"}}
 *           }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"event.id"})
 */
class Joblink
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:joblink_event"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:joblink_event"})
     */
    private $name;

    /**
     * @var string
     * 
     * @ORM\Column(name="slug", type="string", length=255)
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:joblink_event"})
     */
    private $slug;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:collection_joblink"})
     */
    private $description;

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
    private $pdfPath;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="joblink", fileNameProperty="pdfPath")
     * 
     * @Assert\File(
     *     maxSize = "2000k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Merci de charger un fichier PDF, taille maximale 2Mo"
     * )
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity="JoblinkSession", mappedBy="joblink",cascade={"persist"})
     * @Groups({"read:collection_joblink"})
     */
    private $joblinkSessions;

    /**
     * @ORM\ManyToMany(targetEntity="EventJobs",inversedBy="joblinks",cascade={"persist"})
     * @ORM\JoinTable(name="events_joblinks")
     */
    private $events;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:joblink_event"})
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo_joblink", fileNameProperty="logo")
     * @var File
     */
    private $logoFile;

    /**
     * Get logo
     *
     * @return \App\Entity\Image
     */
    public function getLogo(): ?string
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
    public function getLogoFile(): ?File
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

    public function __construct()
    {
    	$this->events = new ArrayCollection();
        $this->updatedAt = new \DateTime;
    }

    public function __toString()
    {
        return $this->name;
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
    * Add joblinkSession
    *
    * @param \App\Entity\JoblinkSession $joblinkSession
    *
    * @return JoblinkSessionType
    */
    public function addJoblinkSession(\App\Entity\JoblinkSession $joblinkSession)
    {
        $this->joblinkSessions[] = $joblinkSession;
        $joblinkSession->setJoblink($this);

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getPdfPath()
    {
        return $this->pdfPath;
    }

    /**
     * @return string
     */
    public function getPdfWebPath()
    {
        return '/uploads/joblinks/'.$this->pdfPath;
    }

    /**
     * @param string $pdfPath
     *
     * @return self
     */
    public function setPdfPath($pdfPath)
    {
        $this->pdfPath = $pdfPath;

        return $this;
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
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
}
