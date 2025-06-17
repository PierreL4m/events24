<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 * @Vich\Uploadable
 */
class Email
{
    private $accepted_format = array(
         'image/gif',
         'image/jpeg',
         'image/pjpeg',
         'image/gif',
         'image/png', 
         'application/pdf',
         'application/msword',
         'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
         'application/vnd.oasis.opendocument.text'
    );
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="attachments", fileNameProperty="attachmentPath")
     * 
     * @var File
     */
    private $attachmentFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $attachmentPath;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachmentType;

     /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotNull(message = "Vous devez renseigner le sujet de l'email")
     */
    private $subject;

    /**
     * @var text
     *
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotNull(message = "Vous devez renseigner le corps du mail")
     */
    private $body;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sent;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="emails")
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="EmailType", inversedBy="emails")
     */
    private $emailType;

     /**
     * @ORM\OneToMany(targetEntity="Recipient", mappedBy="emailEntity", orphanRemoval=true)
     */
    private $recipients;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setAttachmentFile(?File $image = null): void
    {
        $this->attachmentFile = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();

            if (in_array($image->getMimeType(),$this->accepted_format)){
                 $this->attachmentType = $image->getMimeType();
            }
            else{
                throw new \Exception('Attachement file '.$image->getMimeType().' not handled');
            }
        }
    }

    public function getAttachmentFile(): ?File
    {
        return $this->attachmentFile;
    }

    /**
     * @return string
     */
    public function getAttachmentPath()
    {
        return $this->attachmentPath;
    }

     /**
     * @return string
     */
    public function getAttachmentWebPath()
    {
        return '/uploads/attachments/'.$this->attachmentPath;
    }

    /**
     * @param string $attachmentPath
     *
     * @return self
     */
    public function setAttachmentPath($attachmentPath)
    {
        $this->attachmentPath = $attachmentPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return text
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param text $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param mixed $sent
     *
     * @return self
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

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
     * @return mixed
     */
    public function getEmailType()
    {
        return $this->emailType;
    }

    /**
     * @param mixed $emailType
     *
     * @return self
     */
    public function setEmailType($emailType)
    {
        $this->emailType = $emailType;

        return $this;
    }
      /**
    * Add recipient
    *
    * @param \App\Entity\Recipient $recipient
    *
    * @return RecipientType
    */
    public function addRecipient(\App\Entity\Recipient $recipient)
    {
        $this->recipients[] = $recipient;
        $recipient->setEmail($this);
    
        return $this;
    }

    /**
    * Remove recipient
    *
    * @param \App\Entity\Recipient $recipient
    */
    public function removeRecipient(\App\Entity\Recipient $recipient)
    {
        $this->recipients->removeElement($recipient);   
    }

    /**
    * Get recipients
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getRecipients()
    {
        return $this->recipients;
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
    public function getAttachmentType()
    {
        return $this->attachmentType;
    }

    /**
     * @param string $attachmentType
     *
     * @return self
     */
    public function setAttachmentType($attachmentType)
    {
        $this->attachmentType = $attachmentType;

        return $this;
    }

    /**
     * @param mixed $recipients
     *
     * @return self
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcceptedFormat()
    {
        return $this->accepted_format;
    }
}
