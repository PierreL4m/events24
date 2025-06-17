<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationFormationSimpleRepository")
 */
class ParticipationFormationSimple extends Participation
{
    /*
    * @Groups({"participation"})
    */
    protected $label = 'Formations proposées' ;

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getLabelColon()
    {
        return $this->label." : ";
    }


  	/**
     * @var text
     *
     * @ORM\Column(type="text")
     */
    private $description;   

    public function copyChild(Participation $p)
    {
        $this->setDescription($p->getDescription());
    }
    public function getType()
    {
        return $this->type_label.'centre de formation';
    }
    /**
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param text $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
