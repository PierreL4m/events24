<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\EventSimpleRepository")
 */
class EventSimple extends Event
{
	 /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $startBreak;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $endBreak;

    /**
     * @return mixed
     */
    public function getEndBreak()
    {
        return $this->endBreak;
    }

    /**
     * @param mixed $endBreak
     *
     * @return self
     */
    public function setEndBreak($endBreak)
    {
        $this->endBreak = $endBreak;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartBreak()
    {
        return $this->startBreak;
    }

    /**
     * @param mixed $startBreak
     *
     * @return self
     */
    public function setStartBreak($startBreak)
    {
        $this->startBreak = $startBreak;

        return $this;
    }
}
