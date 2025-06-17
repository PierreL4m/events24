<?php

namespace App\Entity;

use App\Model\ImageInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */

class Image implements ImageInterface
{
    protected $tmp_path ;
    protected $tmp_path_origin ;
    protected $keep_origin;
    protected $format;

    public function setTmpPath($tmp_path)
    {
        $this->tmp_path = $tmp_path;       
    }

    public function getTmpPath()
    {
        return $this->tmp_path;
    }

    public function getAbsoluteTmpPath()
    {
        return $this->getUploadRootDir().'/'.$this->tmp_path;
    }

    public function setTmpPathOrigin($tmp_path_origin)
    {
        $this->tmp_path_origin = $tmp_path_origin;       
    }

    public function getTmpPathOrigin()
    {
        return $this->tmp_path_origin;
    }

    public function getAbsoluteTmpPathOrigin()
    {
        return $this->getUploadRootDir().'/'.$this->tmp_path_origin;
    }

    public function setKeepOrigin($keep_origin)
    {
        $this->keep_origin = $keep_origin;
    }

    public function keepOrigin()
    {
        return $this->keep_origin;
    }

    public function setUp($upload_dir,$width=50,$height=null,$box=null,$format=null)
    {
        $this->uploadDir = $upload_dir;
        $this->width = $width;
        if ($height == null){
            $height=$width;
        }
        $this->height = $height;

        if ($box){
            $this->box = $box;
        }

        if ($format){
            $this->format = $format;
        }
        return $this;
    }
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     * @Groups({"read:collection"})
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:get_participation"})
     * @Groups({"read:joblink_event"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:exposant_list"})
     */
    protected $path;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     * @Groups({"read:incomingEvents"})
     */
    protected $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="original_path", type="string", length=255, nullable=true)
     * @Groups({"read:collection"})
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:joblink_event"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:incomingEvents"})
     */
    protected $originalPath;

    /**
     * @var string
     *
     * @ORM\Column(name="upload_dir", type="string", length=255)
     * @Groups({"read:collection"})
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:joblink_event"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    protected $uploadDir;

    /**
     * @var \DateTime $updated
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer",nullable=true)
     */
    protected $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer",nullable=true)
     */
    protected $height;

    /**
     * @var boolean
     *
     * @ORM\Column(name="box", type="boolean",nullable=true)
     */
    protected $box;

    /**
     * @Assert\File(
     *     maxSize="6000000",
     *     mimeTypes = {
     *         "application/pdf", 
     *         "application/illustrator",
     *         "image/x-eps",
     *         "image/vnd.adobe.photoshop",
     *         "image/gif",     
     *         "image/png",
     *         "image/jpeg",              
     *         "application/postscript"
     *      },
     *     mimeTypesMessage = "Votre image n'est pas valide. Merci de réesayer avec une image au format jpg, png, gif, eps, psd, pdf ou ai."
     *     )
     */
    protected $file;

    public function __toString()
    {
        return (string) $this->getId();
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set size
     *
     * @param array $size
     *
     * @return Image
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return array
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt(string $alt, string $path = '')
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set originalPath
     *
     * @param string $originalPath
     *
     * @return Image
     */
    public function setOriginalPath($originalPath)
    {
        $this->originalPath = $originalPath;

        return $this;
    }

    /**
     * Get originalPath
     *
     * @return string
     */
    public function getOriginalPath()
    {
        return $this->originalPath;
    }

    /**
     * Set updated
     *
     * @param string $updated
     *
     * @return Image
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set uploaDir
     *
     * @param string $uploaddir
     *
     * @return Image
     */
    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;

        return $this;
    }

    /**
     * Get uploaddir
     *
     * @return string
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     *
     * @return Image
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getAbsolutePathOrigin()
    {
        return null === $this->originalPath
            ? null
            : $this->getUploadRootDir().'/'.$this->originalPath;
    }

    /**
     * @Groups({"read:collection"})
     * @Groups({"read:collection_joblink"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:joblink_event"})
     * @SerializedName("path")
     */
    protected $webPath;
    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->path;
    }

    public function getWebPathOrigin()
    {
        return null === $this->originalPath
            ? null
            : '/'.$this->getUploadDirPath().'/'.$this->originalPath;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../../public/'.$this->getUploadDirPath();
    }

    protected function getUploadDirPath()
    {
        return 'uploads/'.$this->uploadDir;
    }

    /**
     * Set width
     *
     * @param int $width
     *
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param int $height
     *
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set box
     *
     * @param boolean $box
     *
     * @return Image
     */
    public function setBox($box)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return boolean
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $format
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }
}
