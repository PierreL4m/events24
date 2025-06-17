<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationCompanySimpleRepository")
 * @Vich\Uploadable
 */
class ParticipationCompanySimple extends Participation
{
    /*
    * @Groups({"participation"})
    */
  	protected $label = 'Postes à Pourvoir (H/F)' ;

  	/**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getLabelColon()
    {
        return $this->label." : ";
    }
    /*
    * @Groups({"participation"})
    */
    protected $contract_type_label = 'Type de contrat(s)' ;

    /**
     * @return string
     */
    public function getContractTypeLabel()
    {
        return $this->contract_type_label;
    }

    public function getType()
    {
        return $this->type_label.'entreprise';
    }
  	/**
     * @var text
     *
     * @ORM\Column(type="text")
     */
    private $description;

     /**
     * @ORM\ManyToMany(targetEntity="ContractType",inversedBy="participations")
     * @ORM\JoinTable("participation_company_simple_contract_type")
     */
    private $contractTypes;

	public function __construct()
    {
        $this->contractTypes = new ArrayCollection();
    }
    public function copyChild(Participation $p)
    {
        $this->contractTypes = [];

        foreach ($p->getContractTypes() as $ct) {
            $this->addContractType($ct);
        }
        $this->setDescription($p->getDescription());

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

     /**
    * Add contractType
    *
    * @param \App\Entity\ContractType $contractType
    *
    * @return ContractTypeType
    */
    public function addContractType(\App\Entity\ContractType $contractType)
    {
        $this->contractTypes[] = $contractType;

        return $this;
    }

    /**
    * Remove contractType
    *
    * @param \App\Entity\ContractType $contractType
    */
    public function removeContractType(\App\Entity\ContractType $contractType)
    {
        if ($this->contractTypes->contains($contractType)) {
            $this->contractTypes->removeElement($contractType);
        }
    }

    /**
    * Get contractTypes
    *
    * @return \Doctrine\Common\Collections\Collection
    */
    public function getContractTypes()
    {
        return $this->contractTypes;
    }
}
