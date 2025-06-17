<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BilanFileTypeRepository")
 */
class BilanFileType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_events"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_events"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_events"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_events"})
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_events"})
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="BilanFile", mappedBy="bilanFileType", cascade={"persist"})
     */
    private $bilanFiles;

    public function __construct()
    {       
        $this->bilanFiles = new ArrayCollection();
    }   

    public function __toString()
    {
        return $this->name;
    }

    /**
	* Add BilanFile
	*
	* @param \App\Entity\BilanFile $BilanFile
	*
	* @return BilanFileType
	*/
	public function addBilanFile(\App\Entity\BilanFile $BilanFile)
	{
		$this->BilanFiles[] = $BilanFile;
	
		return $this;
	}

	/**
	* Remove BilanFile
	*
	* @param \App\Entity\BilanFile $BilanFile
	*/
	public function removeBilanFile(\App\Entity\BilanFile $BilanFile)
	{
		$this->BilanFiles->removeElement($BilanFile);	
	}

	/**
	* Get BilanFiles
	*
	* @return \Doctrine\Common\Collections\Collection
	*/
	public function getBilanFiles()
	{
		return $this->BilanFiles;
	}


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

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
}
