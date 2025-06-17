<?php
namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Appe\Entity\Image;
use App\Helper\ImageHelper;
/**
 * @author France Benoit
 */
class UploadSubscriber implements EventSubscriber
{
    const ACCEPTED_EXTENTIONS = ['jpg', 'png', 'gif'];

    public function __construct(ImageHelper $helper)
    {
        $this->helper = $helper;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
            'preRemove',
            'postPersist',
            'postUpdate'
        );
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $em = $this->getEm($args);
        $image = $args->getEntity();

        if ($em && ($image->getFile())){
            $image->setTmpPath($image->getPath());

            if (!$image->keepOrigin()){
                $image->setTmpPathOrigin($image->getOriginalPath());
            }
            $this->setVars($image);
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $em = $this->getEm($args);
        $image = $args->getEntity();

        if($em && ($image->getFile())){
            if (is_null($image->getAlt())){
                $this->setAlt($image);
            }
            $this->setVars($image);
        }
    }

    public function setVars($image)
    {
        $this->setUpdated($image);
        $this->setPath($image);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        // $em = $this->getEm($args);
        // $image = $args->getEntity();

        // if ($em){
        //     $images = $em->getRepository('Image')->findByPath($image->getPath());
        //     if (count($images) == 1){
        //         if($image->getPath() && $image->getPath() != ""){
        //             $file = $image->getAbsolutePath();

        //             if (file_exists($file)) {
        //                 unlink($file);
        //             }
        //         }
        //         if($image->getAbsolutePath() && $image->getAbsolutePath() != ""){
        //             $file = $image->getAbsolutePathOrigin();
        //             if (file_exists($file)) {
        //                 unlink($file);
        //             }
        //         }
        //     }
        // }
    }

    public function postPersist(LifecycleEventArgs $args)
    {

        $em = $this->getEm($args);

        $image = $args->getEntity();

        if ($em && (!empty($image->getFile()))){
            $this->helper->resizeAndCrop($image);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $em = $this->getEm($args);

        $image = $args->getEntity();

        if ($em && !empty($image->getFile())){
            $this->helper->resizeAndCrop($image);

            $images = $em->getRepository('Image')->findByPath($image->getTmpPath());

            if (empty($images)){
                $file = $image->getAbsoluteTmpPath();
                if (file_exists($file) && is_file($file)) {
                    unlink($file);
                }

                if (!$image->keepOrigin()){
                    $file = $image->getAbsoluteTmpPathOrigin();
                    if (file_exists($file) && is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }
    /**
     * check if entity is Image, return $em if so
     * @param  LifecycleEventArgs
     * @return EntityManager if entity instance of image, else false
     */
    public function getEm(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        // only act on some "Image" entity
        if ($entity instanceof Image) {
            return $args->getEntityManager();
        }
        else{
            return false;
        }
    }
    /**
     * @param entity image
     */
    public function setUpdated($image)
    {
        $image->setUpdated(new \Datetime());
    }

    /**
     * @param entity image
     */
    public function setPath($image)
    {
        $filename = $this->helper->EscapeFrenchChar($image->getFile()->getClientOriginalName()."_".uniqid());
        $filename = preg_replace('#[^a-z0-9_]#', '_', mb_strtolower($filename));

        $extension = strtolower($image->getFile()->getClientOriginalExtension());

        if ($extension == 'eps' || $extension == 'psd' || $extension == 'ai'){
            $image->setPath($filename.".jpg");
        }
        elseif($image->getFormat() && in_array($image->getFormat(), $this::ACCEPTED_EXTENTIONS)){
            $image->setPath($filename.".".$image->getFormat());
        }
        else{
            $image->setPath($filename.".".$extension);
        }

        if (!$image->keepOrigin()){
            $image->setOriginalPath($filename."_origin.".$extension);
        }
        if(!$image->getWidth()){
            $image->setPath($image->getOriginalPath());
        }
    }

    public function setAlt($image)
    {
        $file = $image->getFile();
        $pos = strrpos($file->getClientOriginalName(), '.');
        $name = substr($file->getClientOriginalName(), 0, $pos);
        $name = preg_replace('/-|_/', ' ', $name);
        $image->setAlt($name);
    }
}
