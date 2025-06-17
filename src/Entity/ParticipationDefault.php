<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationDefaultRepository")
 */
class ParticipationDefault extends Participation
{
    public function copyChild(Participation $participation){}

    public function getType()
    {
        return $this->type_label.'simple';
    }
}
