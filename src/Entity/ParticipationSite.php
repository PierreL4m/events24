<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationSiteRepository")
 */
class ParticipationSite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *  @Groups({"read:collection_participation"})
     * @Groups({"read:collection_scanned"})
     
     * @Groups({"read:get_participation"})
     * @Groups({"read:get_jobs_participation"})
     * @Groups({"read:get_job_participation"})
     * @Groups({"read:exposant_list"})
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:get_participation"})
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="Participation", inversedBy="sites")
     */
    private $participation;

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
    public function getUrl()
    {
        if ($this->url) {
            $last_char = substr($this->url, -1);

            if ($last_char == '/') {
                $this->url = substr($this->url, 0, (strlen($this->url) - 1));
            }
        }
        return $this->url;
    }

    public function getUrlForApi()
    {
        if ($this->label) {
            return $this->label;
        } else {
            $url = $this->getUrl();
            $url = str_replace('https://', '', $url);
            $url = str_replace('http://', '', $url);

            return $url;
        }
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setUrl($url)
    {
        if ($url) {
            $last_char = substr($url, -1);

            if ($last_char == '/') {
                $url = substr($url, 0, (strlen($url) - 1));
            }
        }
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParticipation()
    {
        return $this->participation;
    }

    /**
     * @param mixed $participation
     *
     * @return self
     */
    public function setParticipation($participation)
    {
        $this->participation = $participation;

        return $this;
    }
}
