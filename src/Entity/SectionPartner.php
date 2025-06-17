<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionPartnerRepository")
 */
class SectionPartner extends Section
{
    // public function getType()
    // {
    //     return 'partner';
    // }
  
}
