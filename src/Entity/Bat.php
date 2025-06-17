<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BatRepository")
 * @Vich\Uploadable
 */
class Bat
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
     */
    private $path;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="bat", fileNameProperty="path")
     * 
     * @Assert\File(
     *     maxSize = "2000k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Merci de charger un fichier PDF, taille maximale 2Mo"
     * )
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
     * @ORM\Column(type="string", length=255)
     */
    private $pageLabel;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="bats")
     */
    private $event;

	/**
     * @ORM\ManyToMany(targetEntity="Participation",inversedBy="bats",cascade={"persist","remove"})
     * @ORM\JoinTable()
     */
    private $participations;

     /**
     * Constructor
     */
    public function __construct()
    {       
        $this->participations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->pageLabel;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getPathSrc()
    {
        return 'uploads/bats/'.$this->path;
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
     * @return string
     */
    public function getPageLabel()
    {
        return $this->pageLabel;
    }

    /**
     * @param string $pageLabel
     *
     * @return self
     */
    public function setPageLabel($pageLabel)
    {
        $this->pageLabel = $pageLabel;

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
	* Add participation
	*
	* @param \App\Entity\Participation $participation
	*
	* @return ParticipationType
	*/
	public function addParticipation(\App\Entity\Participation $participation)
	{
        if ($this->participations->contains($participation)){
            return $this;
        }
		$this->participations[] = $participation;
		$participation->addBat($this);
	
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
		$participation->removeBat($this);	
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
}
