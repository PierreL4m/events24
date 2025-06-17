<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientUserRepository")
 */
abstract class ClientUser extends User
{
    public function getType() 
    {
        return 'Exposant';
    }
}
