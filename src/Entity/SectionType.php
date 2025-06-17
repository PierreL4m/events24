<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionTypeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 * 
 */
class SectionType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_concept_section"})
     */
    private $slug;



    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $automaticGeneration;

     /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $defaultOnPublic;

     /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $defaultOnCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     */
    private $sectionClass;

     /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="sectionType", cascade={"all"})
     */
    private $sections;

    public function __construct()
    {
    	$this->sections = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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
    public function getAutomaticGeneration()
    {
        return $this->automaticGeneration;
    }

    /**
     * @param mixed $automaticGeneration
     *
     * @return self
     */
    public function setAutomaticGeneration($automaticGeneration)
    {
        $this->automaticGeneration = $automaticGeneration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultOnPublic()
    {
        return $this->defaultOnPublic;
    }

    /**
     * @param mixed $defaultOnPublic
     *
     * @return self
     */
    public function setDefaultOnPublic($defaultOnPublic)
    {
        $this->defaultOnPublic = $defaultOnPublic;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultOnCity()
    {
        return $this->defaultOnCity;
    }

    /**
     * @param mixed $defaultOnCity
     *
     * @return self
     */
    public function setDefaultOnCity($defaultOnCity)
    {
        $this->defaultOnCity = $defaultOnCity;

        return $this;
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
        $section->setSectionType($this);
	
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
    public function getSectionClass()
    {
        return $this->sectionClass;
    }

    /**
     * @param string $sectionClass
     *
     * @return self
     */
    public function setSectionClass($sectionClass)
    {
        $this->sectionClass = $sectionClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiTitle()
    {
        if ($this->apiTitle){
            return $this->apiTitle;
        }
        elseif ($this->apiTitle){
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
