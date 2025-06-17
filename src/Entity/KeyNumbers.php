<?php

namespace App\Entity;

use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KeyNumbersRepository")
 */
class KeyNumbers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     */
    private $exposants;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     */
    private $offres;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     */
    private $candidats;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_place"})
     * @Groups({"read:incoming_events"})
     * @Groups({"read:collection_events"})
     * @Groups({"read:event_exposant"})
     * @Groups({"read:incoming_events"})
     */
    private $entretiens;

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
    public function getExposants()
    {
        return $this->exposants;
    }

    /**
     * @param mixed $exposants
     *
     * @return self
     */
    public function setExposants($exposants)
    {
        $this->exposants = $exposants;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOffres()
    {
        return $this->offres;
    }

    /**
     * @param mixed $offres
     *
     * @return self
     */
    public function setOffres($offres)
    {
        $this->offres = $offres;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCandidats()
    {
        return $this->candidats;
    }

    /**
     * @param mixed $candidats
     *
     * @return self
     */
    public function setCandidats($candidats)
    {
        $this->candidats = $candidats;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntretiens()
    {
        return $this->entretiens;
    }

    /**
     * @param mixed $entretiens
     *
     * @return self
     */
    public function setEntretiens($entretiens)
    {
        $this->entretiens = $entretiens;

        return $this;
    }
}
