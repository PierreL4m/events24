<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantSectionRepository")
 * @Vich\Uploadable
 */
class ParticipantSection
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
     * @ORM\Column(type="text",nullable=true)
     
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SectionParticipant", inversedBy="participants", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     
     */
    private $section;

    /**
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist", "remove"})
     */
    private $logo;

    /**
     * @Vich\UploadableField(mapping="logo_participants_sections", fileNameProperty="logo")
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

    public function setLogoFile(?string $logoFile = null)
    {
        $this->logoFile = $logoFile;
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
    public function getPlanning()
    {
        return $this->planning;
    }

    /**
     * @param string $planning
     *
     * @return self
     */
    public function setPlanning($planning)
    {
        $this->planning = $planning;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     *
     * @return self
     */
    public function setSection($section)
    {
        $this->section = $section;

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
}
