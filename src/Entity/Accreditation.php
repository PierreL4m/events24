<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\Api\GetAccredByMail;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccreditationRepository")
 * @ORM\Table(name="accreditation")
 * @Vich\Uploadable
 * @ApiResource(
 *     collectionOperations={
 *           "get"={
 *               "normalization_context"={"groups"={"read:collection_accred"}}
 *            }
 *     },
 *     itemOperations={
 *           "get"={
 *               "normalization_context"={"groups"={"read:collection_accred"}}
 *            },
 *            "get_accreditation_mail"={
 *                "method" ="GET",
 *                "path" = "/accredByMail/{data}",
 *                "controller"=GetAccredByMail::class,
 *                "read"=false,
 *                "normalization_context"={"groups"={"read:get_accreditation_mail"}}
 *            }
 *     }
 * )
 */
class Accreditation
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"read:accreditation_add"})
     * @Groups({"read:get_accreditation_mail"})
     * @Groups({"read:collection_accred"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @ Assert\NotNull(
     *     message = "Merci de renseigner votre nom"
     * )
     * @Groups({"read:accreditation_add"})
     * @Groups({"read:get_accreditation_mail"})
     * @Groups({"read:collection_accred"})
     */
    private $lastname;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * @ Assert\NotNull(
     *     message = "Merci de renseigner votre prénom"
     * )
     * @Groups({"read:accreditation_add"})
     * @Groups({"read:get_accreditation_mail"})
     * @Groups({"read:collection_accred"})
     */
    private $firstname;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * @ Assert\NotNull(
     *     message = "Merci de renseigner votre mail"
     * )
     * @Assert\Email(
     * *     message = "Merci de renseigner un mail valide"
     * * )
     * @Groups({"read:accreditation_add"})
     * @Groups({"read:get_accreditation_mail"})
     * @Groups({"read:collection_accred"})
     */
    private $email;

    /**
     *
     * @ORM\Column(type="string", length=255)
     * @ Assert\NotNull(
     *     message = "Merci de renseigner votre numéro de téléphone"
     * )
     * @Assert\Length(
     *      min = 10,
     *      max = 20,
     *      minMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres",
     *      maxMessage = "Merci de rentrer un numéro de téléphone valide à 10 chiffres"
     * )
     * @Groups({"read:accreditation_add"})
     * @Groups({"read:get_accreditation_mail"})
     * @Groups({"read:collection_accred"})
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $event;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Participation")
     * @ORM\JoinColumn(nullable=true,onDelete="CASCADE")
     */
    private $participation;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true)

     */

    private $accreditationPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255,nullable=true)

     */
    private $qrCode;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set event
     *
     * @param \App\Entity\Event $event
     *
     * @return Participation
     */
    public function setEvent(\App\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \App\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set event
     *
     * @param \App\Entity\Participation $participation
     *
     * @return Participation
     */
    public function setParticipation(\App\Entity\Participation $participation = null)
    {
        $this->participation = $participation;

        return $this;
    }

    /**
     * Get participation
     *
     * @return \App\Entity\Participation
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = ucwords($firstname);
    }
    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = mb_strtoupper($lastname);
    }

    /**
     * @return integer
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param integer $phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $this->formatPhone($phone);

        return $this;
    }

    private function formatPhone($phone)
    {

        $phone = preg_replace('#\D#U', '', $phone);
        if(strlen($phone) != 10) return $phone;

        for($pos = 8; $pos > 0; $pos-= 2){
            $phone = substr($phone, 0, $pos) .'.'. substr($phone, $pos);
        }
        return $phone;
    }

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccreditationPath()
    {
        if ($this->accreditationPath) {
            return '/accreditations/' . $this->accreditationPath;
        }
        return null;
    }

    /**
     * @param string $accreditationPath
     *
     * @return self
     */
    public function setAccreditationPath($accreditationPath)
    {
        $this->accreditationPath = $accreditationPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @param string $qrCode
     *
     * @return self
     */
    public function setQrCode($qrCode)
    {
        $this->qrCode = $qrCode;

        return $this;
    }
    final public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __unserialize(array $data): void
    {
        foreach($data as $k => $v) {
            $this->$k = $v;
        }
    }
}
