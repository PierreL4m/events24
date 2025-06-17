<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
/**
 * ResponsableBis
 *
 * @ORM\Table(name="responsablesbis")
 * @ORM\Entity(repositoryClass="App\Repository\ResponsableBisRepository")
 */
class ResponsableBis
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ExposantUser", inversedBy="responsableBises", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $responsable;
    
    public function __toString()
    {
        return $this->email;
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
     * Set email
     *
     * @param string $email
     * @return ResponsableBis
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set responsable
     *
     * @param App\Entity\User $responsable
     * @return ResponsableBis
     */
    public function setResponsable(User $responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return App\Entity\User 
     */
    public function getResponsable()
    {
        return $this->responsable;
    }
}
