<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"section_simple" = "SectionSimple","section_participant" = "SectionParticipant","section_agenda" = "SectionAgenda","section_partner" = "SectionPartner","section_joblink" = "SectionJoblink","section_sector" = "SectionSector"})
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * @Vich\Uploadable
 */
abstract class Section
{
    public function getType()
    {
        $reflexionClass = new \ReflectionClass($this);
        $shortName = $reflexionClass->getShortName();

        return substr($shortName, 7);
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:incomingEvents"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $menuTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apiTitle;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     */
    private $sOrder;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     *
     */
    private $lastUpdate;

    /**
     * @return string
     */
    public function getlastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param mixed $lastUpdate
     *
     * @return self
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10,nullable=true)
     */
    private $imgPosition;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     * @Groups({"read:incomingEvents"})
     */
    private $onPublic;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_concept_section"})
     */
    private $onCity;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read:get_concept_section"})
     */
    private $onBilan;

    /**
     * @ORM\ManyToOne(targetEntity="SectionType", inversedBy="sections")
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $sectionType;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="sections", cascade={"persist"})
     */
    private $event;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $imageName;

    /**
     * @Vich\UploadableField(mapping="image_section", fileNameProperty="imageName")
     * @var File
     */
    private $imageFile;

    /**
     * Get banner
     *
     * @return \App\Entity\Image
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \App\Entity\Image $image
     *
     * @return Logo
     */
    public function setImage(\App\Entity\Image $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get imageFile
     *
     * @return File|UploadedFile
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Set imageFile
     *
     * @param File|UploadedFile $imageFile
     *
     * @return Post
     */
    public function setImageFile($imageFile = null)
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
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

    public function getImageName()
    {
        return $this->imageName;
    }


    public function setImageName(string $imageName) : self
    {
        $this->imageName = $imageName;

        return $this;
    }


    public function __toString()
    {
        return $this->title;
    }

    public function __clone()
    {
        $this->sectionType = $this->sectionType;
        //  $this->image = clone $this->image;
    }

    public function simpleCopy()
    {
        $class_name = get_class($this);
        $section = new $class_name;
        $section->setSectionType($this->getSectionType());
        $section->setSectionTypeData();
        $section->setsOrder(0);

        return $section;
    }

    public function getTypeSlug()
    {
        return $this->sectionType->getSlug();
    }

    public function setSectionTypeData()
    {
        $sectionType = $this->sectionType;

        if (!$sectionType) {
            throw new \Exception('Cannot set section type data to section because there is no section type');
        }
        $this->title = $sectionType->getTitle();
        $this->menuTitle = $sectionType->getMenuTitle();
        $this->slug = $sectionType->getMenuTitle();
        $this->onPublic = $sectionType->getDefaultOnPublic();
        $this->onCity = $sectionType->getDefaultOnCity();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
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
     * @return mixed
     */
    public function getSorder()
    {
        return $this->sOrder;
    }

    /**
     * @param mixed $sOrder
     *
     * @return self
     */
    public function setSorder($sOrder)
    {
        $this->sOrder = $sOrder;

        return $this;
    }


    /**
     * @return string
     */
    public function getImgPosition()
    {
        return $this->imgPosition;
    }

    /**
     * @param string $imgPosition
     *
     * @return self
     */
    public function setImgPosition($imgPosition)
    {
        $this->imgPosition = $imgPosition;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnPublic()
    {
        return $this->onPublic;
    }

    /**
     * @param mixed $onPublic
     *
     * @return self
     */
    public function setOnPublic($onPublic)
    {
        $this->onPublic = $onPublic;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnCity()
    {
        return $this->onCity;
    }

    /**
     * @param mixed $onCity
     *
     * @return self
     */
    public function setOnCity($onCity)
    {
        $this->onCity = $onCity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnBilan()
    {
        return $this->onBilan;
    }

    /**
     * @param mixed $onBilan
     *
     * @return self
     */
    public function setOnBilan($onBilan)
    {
        $this->onBilan = $onBilan;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSectionType()
    {
        return $this->sectionType;
    }

    /**
     * @param mixed $sectionType
     *
     * @return self
     */
    public function setSectionType($sectionType)
    {
        $this->sectionType = $sectionType;

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
     * @return string
     */
    public function getMenuTitle()
    {
        return $this->menuTitle;
    }

    /**
     * @param string $menuTitle
     *
     * @return self
     */
    public function setMenuTitle($menuTitle)
    {
        $this->menuTitle = $menuTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiTitle()
    {
        if ($this->apiTitle) {
            return $this->apiTitle;
        }

        return $this->title;
    }

    /**
     * @param string $apiTitle
     *
     * @return self
     */
    public function setApiTitle($apiTitle)
    {
        $this->apiTitle = $apiTitle;

        return $this;
    }
}
