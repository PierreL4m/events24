<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 */
class Skill
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $id;

     /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
      * @Groups({"read:get_jobs_participation"})
      * @Groups({"read:get_job_participation"})
     */
    private $name;

	/**
     * @ORM\ManyToOne(targetEntity="Job", inversedBy="skills")
     */
    private $job;

     /**
     * @ORM\OneToMany(targetEntity="Grade", mappedBy="skill", cascade={"persist"})
     */
    private $grades;    

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
    	$this->grades = new ArrayCollection();
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
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param mixed $job
     *
     * @return self
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
    * Add grade
    *
    * @param \App\Entity\Grade $grade
    *
    * @return GradeType
    */
    public function addGrade(\App\Entity\Grade $grade)
    {
        $this->grades[] = $grade;
       	$grade->setSkill($this);
       	
        return $this;
    }

    /**
    * Remove grade
    *
    * @param \App\Entity\Grade $grade
    */
    public function removeGrade(\App\Entity\Grade $grade)
    {
        $this->grades->removeElement($grade);
    }

    /**
    * Get grades
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getGrades()
    {
        return $this->grades;
    }
}
