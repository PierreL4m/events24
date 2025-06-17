<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Skill", inversedBy="grades")
     */
    private $skill;

    /**
     * @ORM\ManyToOne(targetEntity="CandidateParticipation", inversedBy="grades")
     */
    private $candidate_participation;

    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * @param mixed $skill
     *
     * @return self
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCandidate()
    {
        return $this->candidate_participation;
    }

    /**
     * @param mixed $candidate_participation
     *
     * @return self
     */
    public function setCandidate(\App\Entity\CandidateUser $candidate_participation)
    {
        $this->candidate_participation = $candidate_participation;

        return $this;
    }
}
