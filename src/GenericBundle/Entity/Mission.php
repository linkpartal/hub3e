<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mission
 *
 * @ORM\Table(name="mission", indexes={@ORM\Index(name="fk_Mission_user1_idx", columns={"Tuteur_id"})})
 * @ORM\Entity
 */
class Mission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Descriptif", type="string", length=255, nullable=true)
     */
    private $descriptif;

    /**
     * @var string
     *
     * @ORM\Column(name="Profil", type="string", length=255, nullable=true)
     */
    private $profil;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=45, nullable=true)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="TypeContrat", type="string", length=45, nullable=true)
     */
    private $typecontrat;

    /**
     * @var \Users
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Tuteur_id", referencedColumnName="id")
     * })
     */
    private $tuteur;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="mission")
     * @ORM\JoinTable(name="mission_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="mission_id", referencedColumnName="id"),
     *     @ORM\JoinColumn(name="mission_Tuteur_id", referencedColumnName="Tuteur_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     *   }
     * )
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Mission
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
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Mission
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set profil
     *
     * @param string $profil
     *
     * @return Mission
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return string
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Mission
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set typecontrat
     *
     * @param string $typecontrat
     *
     * @return Mission
     */
    public function setTypecontrat($typecontrat)
    {
        $this->typecontrat = $typecontrat;

        return $this;
    }

    /**
     * Get typecontrat
     *
     * @return string
     */
    public function getTypecontrat()
    {
        return $this->typecontrat;
    }

    /**
     * Set tuteur
     *
     * @param \GenericBundle\Entity\Users $tuteur
     *
     * @return Mission
     */
    public function setTuteur(\GenericBundle\Entity\Users $tuteur)
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    /**
     * Get tuteur
     *
     * @return \GenericBundle\Entity\Users
     */
    public function getTuteur()
    {
        return $this->tuteur;
    }

    /**
     * Set formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     *
     * @return Mission
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
     * Add user
     *
     * @param \GenericBundle\Entity\Users $user
     *
     * @return Mission
     */
    public function addUser(\GenericBundle\Entity\Users $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \GenericBundle\Entity\Users $user
     */
    public function removeUser(\GenericBundle\Entity\Users $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
