<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Candidate
 *
 * @ORM\Entity(repositoryClass="App\Repository\ScanUserRepository") *
 */
class ScanUser extends User
{
    public function getType()
    {
        return 'Scan';
    }
}