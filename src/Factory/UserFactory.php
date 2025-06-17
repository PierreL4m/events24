<?php

namespace App\Factory;

use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\OnsiteUser;
use App\Entity\RhUser;


class UserFactory
{
    /**
     * Create staticly desired Event
     *
     * @param Place $place 
     *
     * @return Event
     */
    static public function get($type)
    {
        $instance = null;

        switch (strtolower($type)) {

            case 'l4m':
                $instance = new L4MUser();
                break;
            case 'onsite':
                $instance = new OnsiteUser();
                break;
            case 'exposant':
                $instance = new ExposantUser();
                break;
            case 'rh':
                $instance = new RhUser();
                break;

            default:
                throw new \Exception("No UserClass to match : ".$type);
        }

        return $instance;
    }
}
?>