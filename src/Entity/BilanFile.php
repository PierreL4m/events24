<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BilanFileRepository")
 * @Vich\Uploadable
 */
class BilanFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_events"})
     */
    private $id;

     /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="bilan", fileNameProperty="path")
     * 
     * @Assert\File(
     *     maxSize = "50M",
     *     mimeTypes = {"application/pdf", "application/x-pdf","audio/mpeg3", "audio/x-mpeg-3", "audio/mpeg", "video/mpeg", "application/octet-stream","video/mp4","video/x-m4v"},
     *     mimeTypesMessage = "Merci de charger un fichier pdf /mp3 ou mp4. Taille max : 50M"
     * )
      * @Groups({"read:collection_events"})
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Groups({"read:collection_events"})
     */
    private $path;

     /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
      * @Groups({"read:collection_events"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:collection_events"})
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="BilanFileType", inversedBy="bilanFiles")
     * @Groups({"read:collection_events"})
     */
    private $bilanFileType;

	/**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="bilanFiles")
     */
    private $event;

     /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime;
    }
    public function __toString()
    {
        return $this->name;
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->bilanFileType->getType();
    }
    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->bilanFileType->getSlug();
    }

    public function getLabel()
    {
        return $this->bilanFileType->getLabel();
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
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return '/uploads/bilan/'.$this->path;
    }

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
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
     * @return mixed
     */
    public function getBilanFileType()
    {
        return $this->bilanFileType;
    }

    /**
     * @param mixed $bilanFileType
     *
     * @return self
     */
    public function setBilanFileType($bilanFileType)
    {
        $this->bilanFileType = $bilanFileType;

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
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
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
}
