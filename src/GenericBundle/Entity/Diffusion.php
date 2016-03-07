<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Diffusion
 *
 * @ORM\Table(name="diffusion",uniqueConstraints={@UniqueConstraint(name="unique_formation_mission", columns={"formation_id","mission_id"})})
 * @ORM\Entity
 */
class Diffusion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * @var \Formation
     *
     * @ORM\ManyToOne(targetEntity="Formation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="formation_id", referencedColumnName="id")
     * })
     */
    private $formation;

    /**
     * @var \Mission
     *
     * @ORM\ManyToOne(targetEntity="Mission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mission_id", referencedColumnName="id")
     * })
     */
    private $mission;



    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Diffusion
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Diffusion
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     *
     * @return Diffusion
     */
    public function setFormation(\GenericBundle\Entity\Formation $formation)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
     * Get formation
     *
     * @return \GenericBundle\Entity\Formation
     */
    public function getFormation()
    {
        return $this->formation;
    }

    /**
     * Set mission
     *
     * @param \GenericBundle\Entity\Mission $mission
     *
     * @return Diffusion
     */
    public function setMission(\GenericBundle\Entity\Mission $mission)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \GenericBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }
}
