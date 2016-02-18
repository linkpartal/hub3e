<?php
/**
 * Created by PhpStorm.
 * User: KAMAL
 * Date: 17/02/2016
 * Time: 16:35
 */

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Candidature
 *
 * @ORM\Table(name="candidature",uniqueConstraints={@UniqueConstraint(name="unique_formation_user", columns={"formation_id","users_id"})})
 * @ORM\Entity
 */
class Candidature
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
     * @var \DateTime
     *
     * @ORM\Column(name="datecandidature", type="date", nullable=true)
     */
    private $datecandidature;

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
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \ImportCandidat
     *
     * @ORM\ManyToOne(targetEntity="ImportCandidat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id")
     * })
     */
    private $importcandidat;



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
     * @return Candidature
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
     * @return Candidature
     */
    public function setFormation(\GenericBundle\Entity\Formation $formation = null)
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
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Candidature
     */
    public function setUser(\GenericBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \GenericBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set importcandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importcandidat
     *
     * @return Candidature
     */
    public function setImportcandidat(\GenericBundle\Entity\ImportCandidat $importcandidat = null)
    {
        $this->importcandidat = $importcandidat;

        return $this;
    }

    /**
     * Get importcandidat
     *
     * @return \GenericBundle\Entity\ImportCandidat
     */
    public function getImportcandidat()
    {
        return $this->importcandidat;
    }

    /**
     * Set datecandidature
     *
     * @param \DateTime $datecandidature
     *
     * @return Candidature
     */
    public function setDatecandidature($datecandidature)
    {
        $this->datecandidature = $datecandidature;

        return $this;
    }

    /**
     * Get datecandidature
     *
     * @return \DateTime
     */
    public function getDatecandidature()
    {
        return $this->datecandidature;
    }
}
