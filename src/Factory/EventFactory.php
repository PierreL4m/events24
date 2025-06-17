<?php

namespace App\Factory;

use App\Entity\EventJobs;
use App\Entity\EventSimple;
use App\Entity\Place;
use App\Entity\EventType;
use App\Entity\Event;

class EventFactory
{
    /**
     * Create staticly desired Event
     *
     * @param EventType $type
     *
     * @return Event
     */
    static public function get(EventType $type)
    {
        switch ($type->getRegistrationType()) {

            case EventType::REGISTRATION_TYPE_STANDARD : 
                $instance = new EventSimple();
                break;
                
            case EventType::REGISTRATION_TYPE_JOB :
                $instance = new EventJobs();
                break;

            default:
                if($type->getRecruitmentOfficeAllowed()) {
                    $instance = new EventJobs();
                    break;
                }
                throw new \Exception("No EventClass to match event type : ".$type->getShortName().$type->getRegistrationType());
        }

        return $instance;
    }
}
?>