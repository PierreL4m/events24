<?php

namespace App\EventListener;

use App\Entity\Place;
use App\Helper\GlobalHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class PlaceListener
{
    private $helper;
    private $place;
    private $logger;
    private $token;

    public function __construct(GlobalHelper $helper, LoggerInterface $logger, $token,$env)
    {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->token = $token;

        if ($env == 'dev'){
            $this->token = 'pk.eyJ1IjoiZnJhbmNlLWJlbm9pdC1sNG0iLCJhIjoiY2pub3J6cHZxMXlxdDNwczVxcWloMWZvZyJ9.ni7lkWwRO2g1rX2MQhLLfw';
        }
    }
    public function prePersist(LifecycleEventArgs $args)
    {        
        $entity = $args->getObject();

        // only act on some "Place" entity
        if (!$entity instanceof Place) {            
            return;
        }

        if (empty($entity->getLatitude())){
            $this->setCoordinates($entity);
        }
        $this->place = $entity->getName();
        
    }
    
    public function preUpdate(LifecycleEventArgs $args) 
    {
        $entity = $args->getObject();
        
        // only act on some "Place" entity
        if (!$entity instanceof Place) {            
            return;
        }

        if (empty($entity->getLatitude())){
            $this->setCoordinates($entity);
        }
        $this->place = $entity->getName();
    }

    public function preFlush($entity)
    { 
    	if ($entity->getName() != $this->place ){
            $this->setCoordinates($entity);
    	}
    }

    public function setCoordinates($entity)
    {
       
        $address = $entity->getName()."+".$entity->getAddress()."+".$entity->getCp()."+".$entity->getCity().'+France';
        $address = str_replace(" ", "%20", $address); // replace all the white space with "%20" sign to match with mapbox search pattern
 

        $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$address.'.json?limit=1&access_token='.$this->token;
        $response = file_get_contents($url);
        $json = json_decode($response,TRUE); //generate array object from the response from the web
        
      
        if (array_key_exists(0, $json['features'])){
            $coordinates = $json['features'][0]['center'];
            $entity->setLatitude($coordinates[1]);
            $entity->setLongitude($coordinates[0]);
        }
        else{
            $entity->setLatitude(0);
            $entity->setLongitude(0);
        }
    }
}