<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BilanPictureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BilanPicture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:exposant_list"})
     */
    private $id;

     /**
     * @Assert\Image(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg"},
     *     mimeTypesMessage = "Merci de charger un fichier jpg taille max 5Mo"
     * )
      * @Groups({"read:exposant_list"})
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Groups({"read:exposant_list"})
     */
    private $path;

     /**
     * @ORM\ManyToOne(targetEntity="Participation", inversedBy="bilanPictures", cascade={"persist"})
     */
    private $participation;  

   
     public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../public'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/bilan_pictures';
    }    
    
    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function generateName()
    {
        $name = str_replace(" ", "-",$this->participation->getCompanyName());
        setlocale(LC_CTYPE, 'fr_FR.UTF-8');
        $name =  iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $name = preg_replace('#[^a-z0-9_]#', '_', mb_strtolower($name));

        return $name;
    }
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $this->path = $this->generateName()."_".mt_rand(0,1999).'.'.strtolower($this->getFile()->getClientOriginalExtension());
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
      
        $this->file->move($this->getUploadRootDir(),$this->path);
        
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file && file_exists($file) && is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * @param mixed $participation
     *
     * @return self
     */
    public function setParticipation($participation)
    {
        $this->participation = $participation;

        return $this;
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
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }
}
