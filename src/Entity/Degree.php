<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DegreeRepository")
 * @ApiResource(
 *     collectionOperations={
 *          "get"={
 *              "path" = "/degree",
 *              "normalization_context"={"groups"={"read:collection_degree"}}
 *           }
 *     },
 *     itemOperations={
 *          "get"={
 *              "path" = "/degree/{id}",
 *              "normalization_context"={"groups"={"read:collection_degree"}}
 *           }
 *     }
 * )
 */
class Degree
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:collection_degree"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_degree"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     * @Groups({"read:collection_user"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"read:collection_degree"})
     * @Groups({"read:collection_profile"})
     * @Groups({"read:patch_user_device"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="CandidateUser", mappedBy="degree", cascade={"persist"})
     */
    private $candidates;

    public function __construct()
    {
    	$this->candidates = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }
     /**
     * @return mixed
     */
    public function getCandidates()
    {
        return $this->candidates;
    }
    /**
     * Add candidate
     *
     * @param \App\Entity\CandidateUser $candidate
     *
     * @return Job
     */
    public function addCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates[] = $candidate;
        $candidate->setDegree($this);
       
        return $this;
    }

    /**
     * Remove candidate
     *
     * @param \App\Entity\CandidateUser $candidate
     */
    public function removeCandidate(\App\Entity\CandidateUser $candidate)
    {
        $this->candidates->removeElement($candidate);
    }

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

   
}
