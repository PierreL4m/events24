<?php

namespace App\Entity;

use App\Entity\Place;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ColorRepository")
 */
class Color
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */

    private $name;

    /**
     * @ORM\Column(type="string", length=7)
     *
     * @Assert\Regex(
     *     pattern="/^#[A-Fa-f0-9]{6}/",
     *     message="Le code couleur n'est pas valide"
     * )
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:incomingEvents"})
     * @Groups({"read:collection_user"})
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="colors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $place;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
    	if (!$name && !$this->name){
    		$name = 'main';
    	}
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     *
     * @return self
     */
    public function setCode($code)
    {
    	$firstChar = $code[0];

        if ($firstChar != "#"){
            $code = '#'.$code;
        }

        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlace(): Place
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     *
     * @return self
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;

        return $this;
    }
}
