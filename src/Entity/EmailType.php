<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailTypeRepository")
 * @ORM\Table(indexes={@ORM\Index(name="slug_idx", columns={"slug"})})
 */
class EmailType
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
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $days;


    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="emailType", cascade={"all"})
     */
    private $emails;

    public function __construct()
    {       
        $this->emails = new ArrayCollection();
    }

    /**
	* Add email
	*
	* @param \App\Entity\Email $email
	*
	* @return EmailType
	*/
	public function addEmail(\App\Entity\Email $email)
	{
		$this->emails[] = $email;
		$email->setEmailType($this);
	
		return $this;
	}

	/**
	* Remove email
	*
	* @param \App\Entity\Email $email
	*/
	public function removeEmail(\App\Entity\Email $email)
	{
		$this->emails->removeElement($email);	
	}

	/**
	* Get emails
	*
	* @return \Doctrine\Common\Collections\Collection
	*/
	public function getEmails()
	{
		return $this->emails;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param string $days
     *
     * @return self
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }
}
