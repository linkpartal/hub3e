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
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @var string
     *
     * @ORM\Column(name="NomContact", type="string", length=45, nullable=true)
     */
    private $nomcontact;

    /**
     * @var string
     *
     * @ORM\Column(name="PrenomContact", type="string", length=45, nullable=true)
     */
    private $prenomcontact;

    /**
     * @var string
     *
     * @ORM\Column(name="FonctionContact", type="string", length=45, nullable=true)
     */
    private $fonctioncontact;

    /**
     * @var string
     *
     * @ORM\Column(name="TelContact", type="string", length=45, nullable=true)
     */
    private $telcontact;

    /**
     * @var string
     *
     * @ORM\Column(name="EmailContact", type="string", length=45, nullable=true)
     */
    private $emailcontact;

    /**
     * @var string
     *
     * @ORM\Column(name="Intitule", type="string", length=75, nullable=true)
     */
    private $intitule;

    /**
     * @var string
     *
     * @ORM\Column(name="CodeMission", type="string", length=45, nullable=true)
     */
    private $codemission;

    /**
     * @var string
     *
     * @ORM\Column(name="Domaine", type="string", length=45, nullable=true)
     */
    private $domaine;

    /**
     * @var string
     *
     * @ORM\Column(name="Date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="Datedebut", type="date", nullable=true)
     */
    private $datedebut;

    /**
     * @var string
     *
     * @ORM\Column(name="Datefin", type="date", nullable=true)
     */
    private $datefin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Emploi", type="boolean", nullable=true)
     */
    private $emploi;

    /**
     * @var string
     *
     * @ORM\Column(name="Remuneration", type="integer", nullable=true)
     */
    private $remuneration;

    /**
     * @var string
     *
     * @ORM\Column(name="Horaire", type="string", length=45, nullable=true)
     */
    private $horaire;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspendu", type="boolean", nullable=true)
     */
    private $suspendu= false;

    /**
     * @var \Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Etablissement_id", referencedColumnName="id")
     * })
     */
    private $etablissement;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Tuteur_id", referencedColumnName="id")
     * })
     */
    private $tuteur;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Formation", inversedBy="mission")
     * @ORM\JoinTable(name="mission_has_formation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="mission_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="formation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $formation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="mission")
     * @ORM\JoinTable(name="mission_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="mission_id", referencedColumnName="id")
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
        $this->date = new \DateTime();
        $this->formation = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \GenericBundle\Entity\User $tuteur
     *
     * @return Mission
     */
    public function setTuteur(\GenericBundle\Entity\User $tuteur)
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
     * @param \GenericBundle\Entity\User $user
     *
     * @return Mission
     */
    public function addUser(\GenericBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \GenericBundle\Entity\User $user
     */
    public function removeUser(\GenericBundle\Entity\User $user)
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

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Mission
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set codemission
     *
     * @param string $codemission
     *
     * @return Mission
     */
    public function setCodemission($codemission)
    {
        $this->codemission = $codemission;

        return $this;
    }

    /**
     * Get codemission
     *
     * @return string
     */
    public function getCodemission()
    {
        return $this->codemission;
    }

    /**
     * Set domaine
     *
     * @param string $domaine
     *
     * @return Mission
     */
    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return string
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Add formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     *
     * @return Mission
     */
    public function addFormation(\GenericBundle\Entity\Formation $formation)
    {
        $this->formation[] = $formation;

        return $this;
    }

    /**
     * Remove formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     */
    public function removeFormation(\GenericBundle\Entity\Formation $formation)
    {
        $this->formation->removeElement($formation);
    }



    /**
     * @var string
     *
     * @ORM\Column(name="Date", type="date")
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set remuneration
     *
     * @param integer $remuneration
     *
     * @return Mission
     */
    public function setRemuneration($remuneration)
    {
        $this->remuneration = $remuneration;

        return $this;
    }

    /**
     * Get remuneration
     *
     * @return integer
     */
    public function getRemuneration()
    {
        return $this->remuneration;
    }

    /**
     * Set horaire
     *
     * @param string $horaire
     *
     * @return Mission
     */
    public function setHoraire($horaire)
    {
        $this->horaire = $horaire;

        return $this;
    }

    /**
     * Get horaire
     *
     * @return string
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * Set datedebut
     *
     * @param \DateTime $datedebut
     *
     * @return Mission
     */
    public function setDatedebut($datedebut)
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    /**
     * Get datedebut
     *
     * @return \DateTime
     */
    public function getDatedebut()
    {
        return $this->datedebut;
    }

    /**
     * Set datefin
     *
     * @param \DateTime $datefin
     *
     * @return Mission
     */
    public function setDatefin($datefin)
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get datefin
     *
     * @return \DateTime
     */
    public function getDatefin()
    {
        return $this->datefin;
    }

    /**
     * Set emploi
     *
     * @param boolean $emploi
     *
     * @return Mission
     */
    public function setEmploi($emploi)
    {
        $this->emploi = $emploi;

        return $this;
    }

    /**
     * Get emploi
     *
     * @return boolean
     */
    public function getEmploi()
    {
        return $this->emploi;
    }

    /**
     * Set nomcontact
     *
     * @param string $nomcontact
     *
     * @return Mission
     */
    public function setNomcontact($nomcontact)
    {
        $this->nomcontact = $nomcontact;

        return $this;
    }

    /**
     * Get nomcontact
     *
     * @return string
     */
    public function getNomcontact()
    {
        return $this->nomcontact;
    }

    /**
     * Set prenomcontact
     *
     * @param string $prenomcontact
     *
     * @return Mission
     */
    public function setPrenomcontact($prenomcontact)
    {
        $this->prenomcontact = $prenomcontact;

        return $this;
    }

    /**
     * Get prenomcontact
     *
     * @return string
     */
    public function getPrenomcontact()
    {
        return $this->prenomcontact;
    }

    /**
     * Set fonctioncontact
     *
     * @param string $fonctioncontact
     *
     * @return Mission
     */
    public function setFonctioncontact($fonctioncontact)
    {
        $this->fonctioncontact = $fonctioncontact;

        return $this;
    }

    /**
     * Get fonctioncontact
     *
     * @return string
     */
    public function getFonctioncontact()
    {
        return $this->fonctioncontact;
    }

    /**
     * Set telcontact
     *
     * @param string $telcontact
     *
     * @return Mission
     */
    public function setTelcontact($telcontact)
    {
        $this->telcontact = $telcontact;

        return $this;
    }

    /**
     * Get telcontact
     *
     * @return string
     */
    public function getTelcontact()
    {
        return $this->telcontact;
    }

    /**
     * Set emailcontact
     *
     * @param string $emailcontact
     *
     * @return Mission
     */
    public function setEmailcontact($emailcontact)
    {
        $this->emailcontact = $emailcontact;

        return $this;
    }

    /**
     * Get emailcontact
     *
     * @return string
     */
    public function getEmailcontact()
    {
        return $this->emailcontact;
    }

    /**
     * Set etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return Mission
     */
    public function setEtablissement(\GenericBundle\Entity\Etablissement $etablissement = null)
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    /**
     * Get etablissement
     *
     * @return \GenericBundle\Entity\Etablissement
     */
    public function getEtablissement()
    {
        return $this->etablissement;
    }

    /*
     * Utiliser aprés l'insert(persist) dans la base de données.
     */
    public function genererCode()
    {
        if($this->getCodemission()=='' or !$this->getCodemission())
        {
            $code =strval($this->getId());
            $i = count($code);
            while($i<5)
            {
                $code.= '0';
                $i++;
            }
            $characters = range('A','Z');
            $max = count($characters) - 1;
            for ($i = 0; $i < 4; $i++) {
                $rand = mt_rand(0, $max);
                $code .= $characters[$rand];
            }
            $this->setCodemission($code);
        }

    }

    /**
     * Set suspendu
     *
     * @param boolean $suspendu
     *
     * @return Mission
     */
    public function setSuspendu($suspendu)
    {
        $this->suspendu = $suspendu;

        return $this;
    }

    /**
     * Get suspendu
     *
     * @return boolean
     */
    public function getSuspendu()
    {
        return $this->suspendu;
    }
}
