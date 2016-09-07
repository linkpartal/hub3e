<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Mission
 *
 * @ORM\Table(name="missionPublic", indexes={@ORM\Index(name="fk_Mission_user1_idx", columns={"Tuteur_id"})})
 * @ORM\Entity
 */
class MissionPublic
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
     * @ORM\Column(name="TypeContrat", type="string", length=255, nullable=true)
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
     * @var \GenericBundle\Entity\Tier
     *
     * @ORM\ManyToOne(targetEntity="Tier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_TierCreation", referencedColumnName="id")
     * })
     */
    private $tier;


    /**
     * @var string
     *
     * @ORM\Column(name="EmailContact", type="string", length=45, nullable=true)
     */
    private $emailcontact;
    /**
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

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
     * @var integer
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;


    /**
     * @var integer
     *
     * @ORM\Column(name="pourvue", type="integer", nullable=true)
     */
    private $pourvue;

    /**
     * @var string
     *
     * @ORM\Column(name="Metier1", type="string", length=100, nullable=true)
     */
    private $metier1;

    /**
     * @var string
     *
     * @ORM\Column(name="Metier2", type="string", length=100, nullable=true)
     */
    private $metier2;

    /**
     * @var string
     *
     * @ORM\Column(name="Metier3", type="string", length=1000, nullable=true)
     */
    private $metier3;


    /**
     * @var string
     *
     * @ORM\Column(name="Datecreation", type="datetime", nullable=true)
     */
    private $datecreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Datemodification", type="datetime", nullable=true)
     */
    private $datemodification;

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
     * @ORM\Column(name="NbrePoste", type="integer", nullable=true)
     */
    private $nbreposte;

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
     * @var \GenericBundle\Entity\Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Etablissement_id", referencedColumnName="id")
     * })
     */
    private $etablissement;

    /**
     * @var \GenericBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Tuteur_id", referencedColumnName="id")
     * })
     */
    private $tuteur;

    /**
     * @var \GenericBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="apprentit_id", referencedColumnName="id")
     * })
     */
    private $apprentit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Reponsedef", mappedBy="missions")
     */
    private $reponsedef;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reponsedef = new ArrayCollection();
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
     * Set datecreation
     *
     * @param \DateTime $datecreation
     *
     * @return Mission
     */
    public function setDatecreation($datecreation)
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    /**
     * Get datecreation
     *
     * @return \DateTime
     */
    public function getDatecreation()
    {
        return $this->datecreation;
    }

    /**
     * Set datemodification
     *
     * @param \DateTime $datemodification
     *
     * @return Mission
     */
    public function setDatemodification($datemodification)
    {
        $this->datemodification = $datemodification;

        return $this;
    }

    /**
     * Get datemodification
     *
     * @return \DateTime
     */
    public function getDatemodification()
    {
        return $this->datemodification;
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

    /**
     * Set etablissement
     *
     * @param Etablissement $etablissement
     *
     * @return Mission
     */
    public function setEtablissement(Etablissement $etablissement = null)
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

    /**
     * Set tuteur
     *
     * @param \GenericBundle\Entity\User $tuteur
     *
     * @return Mission
     */
    public function setTuteur(User $tuteur = null)
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
     * Set apprentit
     *
     * @param \GenericBundle\Entity\User $apprentit
     *
     * @return Mission
     */
    public function setApprentit(User $apprentit = null)
    {
        $this->apprentit = $apprentit;

        return $this;
    }

    /**
     * Get apprentit
     *
     * @return \GenericBundle\Entity\User
     */
    public function getApprentit()
    {
        return $this->apprentit;
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
     * Add reponsedef
     *
     * @param \GenericBundle\Entity\Reponsedef $reponsedef
     *
     * @return Mission
     */
    public function addReponsedef(Reponsedef $reponsedef)
    {
        $this->reponsedef[] = $reponsedef;

        return $this;
    }

    /**
     * Remove reponsedef
     *
     * @param \GenericBundle\Entity\Reponsedef $reponsedef
     */
    public function removeReponsedef(Reponsedef $reponsedef)
    {
        $this->reponsedef->removeElement($reponsedef);
    }

    /**
     * Get reponsedef
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponsedef()
    {
        return $this->reponsedef;
    }

    /**
     * Set nbreposte
     *
     * @param integer $nbreposte
     *
     * @return Mission
     */
    public function setNbreposte($nbreposte)
    {
        $this->nbreposte = $nbreposte;

        return $this;
    }

    /**
     * Get nbreposte
     *
     * @return integer
     */
    public function getNbreposte()
    {
        return $this->nbreposte;
    }

    public function __toString() {
        return $this->codemission;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Mission
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Mission
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
     * Set pourvue
     *
     * @param integer $pourvue
     *
     * @return Mission
     */
    public function setPourvue($pourvue)
    {
        $this->pourvue = $pourvue;

        return $this;
    }

    /**
     * Get pourvue
     *
     * @return integer
     */
    public function getPourvue()
    {
        return $this->pourvue;
    }

    /**
     * Set metier1
     *
     * @param string $metier1
     *
     * @return Mission
     */
    public function setMetier1($metier1)
    {
        $this->metier1 = $metier1;

        return $this;
    }

    /**
     * Get metier1
     *
     * @return string
     */
    public function getMetier1()
    {
        return $this->metier1;
    }

    /**
     * Set metier2
     *
     * @param string $metier2
     *
     * @return Mission
     */
    public function setMetier2($metier2)
    {
        $this->metier2 = $metier2;

        return $this;
    }

    /**
     * Get metier2
     *
     * @return string
     */
    public function getMetier2()
    {
        return $this->metier2;
    }

    /**
     * Set metier3
     *
     * @param string $metier3
     *
     * @return Mission
     */
    public function setMetier3($metier3)
    {
        $this->metier3 = $metier3;

        return $this;
    }

    /**
     * Get metier3
     *
     * @return string
     */
    public function getMetier3()
    {
        return $this->metier3;
    }

    /**
     * Set tier
     *
     * @param \GenericBundle\Entity\Tier $tier
     *
     * @return MissionPublic
     */
    public function setTier(\GenericBundle\Entity\Tier $tier = null)
    {
        $this->tier = $tier;

        return $this;
    }

    /**
     * Get tier
     *
     * @return \GenericBundle\Entity\Tier
     */
    public function getTier()
    {
        return $this->tier;
    }
}
