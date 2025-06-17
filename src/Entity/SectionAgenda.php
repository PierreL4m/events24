<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionAgendaRepository")
 */
class SectionAgenda extends Section
{
    /**
     * @ORM\OneToMany(targetEntity="Agenda", mappedBy="section", cascade={"all"})
     */
    private $agendas;

    public function __construct()
    {
        $this->agendas = new ArrayCollection();
    }

      /**
    }
    * Add agenda
    *
    * @param \App\Entity\agenda $agenda
    *
    * @return agendaType
    */
    public function addagenda(\App\Entity\agenda $agenda)
    {
        $this->agendas[] = $agenda;
        $agenda->setSection($this);

        return $this;
    }

    /**
    * Remove agenda
    *
    * @param \App\Entity\agenda $agenda
    */
    public function removeagenda(\App\Entity\agenda $agenda)
    {
        $this->agendas->removeElement($agenda);
    }

    /**
    * Get agendas
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getagendas()
    {
        return $this->agendas;
    }
}
