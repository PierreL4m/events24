<?php

namespace App\EventListener;

use App\Entity\BilanFileType;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\OrganizationType;
use App\Entity\Participation;
use App\Entity\Place;
use App\Entity\SectionType;
use App\Helper\GlobalHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Psr\Log\LoggerInterface;

class SlugGenerator
{
    private $helper;   
    private $logger;

    public function __construct(GlobalHelper $helper, LoggerInterface $logger)
    {
        $this->helper = $helper;
        $this->logger = $logger;
    }
    public function prePersist(LifecycleEventArgs $args)
    {        
        $entity = $args->getObject();

        if (!$this->doSetSlug($entity)){
            return;
        }
        if (!$entity->getSlug()){
            $this->setSlug($entity);
        }
        
    }
    public function preUpdate(LifecycleEventArgs $args)
    {        

        $entity = $args->getObject();

        if (!$this->doSetSlug($entity)){
            return;
        }
        
        $this->setSlug($entity);
    }

    public function doSetSlug($entity)
    {
        if (!method_exists($entity, "setSlug") || $entity instanceof Event ) {            
            return false;
        }
        return true;
    }

    public function setSlug($entity)
    {
        if($entity instanceof Participation){
            $entity->setSlug($this->helper->generateSlug($entity->getCompanyName()."-".$entity->getId()));
        }
        elseif ($entity instanceof EmailType){
            return;
        }
        elseif ($entity instanceof SectionType){
            if (!$entity->getSlug()){
                $entity->setSlug($this->helper->generateSlug($entity->getTitle()));
            }
            else{
                return;
            }
        }
        elseif ($entity instanceof BilanFileType){
            if (!$entity->getSlug()){
                $entity->setSlug($this->helper->generateSlug($entity->getName()));
            }
            else{
                return;
            }
        }
        elseif ($entity instanceof OrganizationType){
            if (!$entity->getSlug()){
                $entity->setSlug($this->helper->generateSlug($entity->getName()));
            }
            else{
                return;
            }
        }
        elseif ($entity instanceof Place){
            if (!$entity->getSlug()){           
                $entity->setSlug($this->helper->generateSlugUnderscore($entity->getCity()));
            }
            else{
                return;
            }
        }
        elseif (method_exists($entity, "getCity")){          
            $entity->setSlug($this->helper->generateSlug($entity->getCity()));
        }
        elseif (method_exists($entity, "getName")){      
            $entity->setSlug($this->helper->generateSlug($entity->getName()));
        }
        elseif (method_exists($entity, "getLabel")){      
            $entity->setSlug($this->helper->generateSlug($entity->getLabel()));
        }
        elseif (method_exists($entity, "getTitle")){      
            $entity->setSlug($this->helper->generateSlug($entity->getTitle()));
        }
        else{
            throw new \Exception('no method to create slug'.json_encode(get_class_methods($entity)));
        }
    }
}