<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RhUserRepository")
 *
 */
class RhUser extends User
{
     /**
     * @ORM\ManyToOne(targetEntity="RecruitmentOffice", inversedBy="rhs")
     */
    private $recruitmentOffice;

    public function getType()
    {
        return 'RH';
    }
     /**
     * @return mixed
     */
    public function getRecruitmentOffice()
    {
        return $this->recruitmentOffice;
    }

    /**
     * @param mixed $recruitmentOffice
     *
     * @return self
     */
    public function setRecruitmentOffice(\App\Entity\RecruitmentOffice $recruitmentOffice = null)
    {
        $this->recruitmentOffice = $recruitmentOffice;

        return $this;
    }
}
