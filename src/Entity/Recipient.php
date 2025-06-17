<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipientRepository")
 */
class Recipient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

     /**
     * @ORM\ManyToOne(targetEntity="Email", inversedBy="recipients")
     */
    private $emailEntity;


    /**
     * @return mixed
     */
    public function getEmailEntity()
    {
        return $this->email_entity;
    }

    /**
     * @param mixed $email_entity
     *
     * @return self
     */
    public function setEmailEntity($email_entity)
    {
        $this->email_entity = $email_entity;

        return $this;
    }
}
