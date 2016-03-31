<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Message
 *
 * @ORM\Table(name="rendezVous", uniqueConstraints={@UniqueConstraint(name="unique_RDV", columns={"tuteur","mission","apprenant"})})
 * @ORM\Entity
 */
class RDV
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
     * @var \DateTime
     *
     * @ORM\Column(name="date1", type="datetime", nullable=false)
     */
    private $date1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date2", type="datetime", nullable=true)
     */
    private $date2;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date3", type="datetime", nullable=true)
     */
    private $date3;

    /**
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * @var integer
     *
     * @ORM\Column(name="choixApprenant", type="boolean", nullable=true)
     */
    private $choixApprenant;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tuteur", referencedColumnName="id")
     * })
     */
    private $tuteur;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="apprenant", referencedColumnName="id")
     * })
     */
    private $apprenant;

    /**
     * @var \Mission
     *
     * @ORM\ManyToOne(targetEntity="Mission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mission", referencedColumnName="id")
     * })
     */
    private $mission;

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
     * @return RDV
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
     * Set compterenduApprenant
     *
     * @param string $compterenduApprenant
     *
     * @return RDV
     */
    public function setCompterenduApprenant($compterenduApprenant)
    {
        $this->compterenduApprenant = $compterenduApprenant;

        return $this;
    }

    /**
     * Get compterenduApprenant
     *
     * @return string
     */
    public function getCompterenduApprenant()
    {
        return $this->compterenduApprenant;
    }

    /**
     * Set compterenduTuteur
     *
     * @param string $compterenduTuteur
     *
     * @return RDV
     */
    public function setCompterenduTuteur($compterenduTuteur)
    {
        $this->compterendu_Tuteur = $compterenduTuteur;

        return $this;
    }

    /**
     * Get compterenduTuteur
     *
     * @return string
     */
    public function getCompterenduTuteur()
    {
        return $this->compterendu_Tuteur;
    }

    /**
     * Set tuteur
     *
     * @param \GenericBundle\Entity\User $tuteur
     *
     * @return RDV
     */
    public function setTuteur(\GenericBundle\Entity\User $tuteur = null)
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    /**
     * Get tuteur
     *
     * @return \GenericBundle\Entity\User
     */
    public function getTuteur()
    {
        return $this->tuteur;
    }

    /**
     * Set apprenant
     *
     * @param \GenericBundle\Entity\User $apprenant
     *
     * @return RDV
     */
    public function setApprenant(\GenericBundle\Entity\User $apprenant = null)
    {
        $this->apprenant = $apprenant;

        return $this;
    }

    /**
     * Get apprenant
     *
     * @return \GenericBundle\Entity\User
     */
    public function getApprenant()
    {
        return $this->apprenant;
    }

    /**
     * Set mission
     *
     * @param \GenericBundle\Entity\Mission $mission
     *
     * @return RDV
     */
    public function setMission(\GenericBundle\Entity\Mission $mission = null)
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

    /**
     * Set date1
     *
     * @param \DateTime $date1
     *
     * @return RDV
     */
    public function setDate1($date1)
    {
        $this->date1 = $date1;

        return $this;
    }

    /**
     * Get date1
     *
     * @return \DateTime
     */
    public function getDate1()
    {
        return $this->date1;
    }

    /**
     * Set date2
     *
     * @param \DateTime $date2
     *
     * @return RDV
     */
    public function setDate2($date2)
    {
        $this->date2 = $date2;

        return $this;
    }

    /**
     * Get date2
     *
     * @return \DateTime
     */
    public function getDate2()
    {
        return $this->date2;
    }

    /**
     * Set date3
     *
     * @param \DateTime $date3
     *
     * @return RDV
     */
    public function setDate3($date3)
    {
        $this->date3 = $date3;

        return $this;
    }

    /**
     * Get date3
     *
     * @return \DateTime
     */
    public function getDate3()
    {
        return $this->date3;
    }

    /**
     * Set choixApprenant
     *
     * @param boolean $choixApprenant
     *
     * @return RDV
     */
    public function setChoixApprenant($choixApprenant)
    {
        $this->choixApprenant = $choixApprenant;

        return $this;
    }

    /**
     * Get choixApprenant
     *
     * @return boolean
     */
    public function getChoixApprenant()
    {
        return $this->choixApprenant;
    }
}
