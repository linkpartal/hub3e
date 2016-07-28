<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Infocomplementaire
 *
 * @ORM\Table(name="infocomplementaire")
 * @ORM\Entity
 */
class Infocomplementaire
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
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $datenaissance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="daterecup", type="date", nullable=true)
     */
    private $daterecup;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datecreation", type="datetime", nullable=true)
     */
    private $datecreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datemodification", type="datetime", nullable=true)
     */
    private $datemodification;

    /**
     * @var string
     *
     * @ORM\Column(name="CPNaissance", type="string", length=45, nullable=true)
     */
    private $cpnaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="lieuNaissance", type="string", length=45, nullable=true)
     */
    private $lieunaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=100, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="portable", type="string", length=45, nullable=true)
     */
    private $portable;

    /**
     * @var string
     *
     * @ORM\Column(name="handicape", type="boolean", length=45, nullable=true)
     */
    private $handicape;

    /**
     * @var string
     *
     * @ORM\Column(name="entrepreneur", type="boolean", length=45, nullable=true)
     */
    private $entrepreneur;

    /**
     * @var string
     *
     * @ORM\Column(name="codepostal", type="string", length=45, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="Lienexterne1", type="string", length=45, nullable=true)
     */
    private $Lienexterne1;
    /**
     * @var string
     *
     * @ORM\Column(name="Lienexterne2", type="string", length=45, nullable=true)
     */
    private $Lienexterne2;
    /**
     * @var string
     *
     * @ORM\Column(name="Lienexterne3", type="string", length=45, nullable=true)
     */
    private $Lienexterne3;

    /**
     * @var string
     *
     * @ORM\Column(name="formationactuelle", type="string", length=45, nullable=true)
     */
    private $formationactuelle;

    /**
     * @var string
     *
     * @ORM\Column(name="dernierDiplome", type="string", length=45, nullable=true)
     */
    private $dernierDiplome;

    /**
     * @var integer
     *
     * @ORM\Column(name="mobilite", type="integer", nullable=true)
     */
    private $mobilite;

    /**
     * @var boolean
     *
     * @ORM\Column(name="profilcomplet", type="smallint", nullable=true)
     */
    private $profilcomplet = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="fratrie", type="integer", nullable=true)
     */
    private $fratrie;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permis", type="smallint", nullable=true)
     */
    private $permis;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vehicule", type="boolean", nullable=true)
     */
    private $vehicule;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VillesFranceFree", mappedBy="infocomplementaire")
     */
    private $villesFranceFreeVille;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->villesFranceFreeVille = new ArrayCollection();
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
     * Set datenaissance
     *
     * @param \DateTime $datenaissance
     *
     * @return Infocomplementaire
     */
    public function setDatenaissance($datenaissance)
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    /**
     * Get datenaissance
     *
     * @return \DateTime
     */
    public function getDatenaissance()
    {
        return $this->datenaissance;
    }

    /**
     * Set cpnaissance
     *
     * @param string $cpnaissance
     *
     * @return Infocomplementaire
     */
    public function setCpnaissance($cpnaissance)
    {
        $this->cpnaissance = $cpnaissance;

        return $this;
    }

    /**
     * Get cpnaissance
     *
     * @return string
     */
    public function getCpnaissance()
    {
        return $this->cpnaissance;
    }

    /**
     * Set lieunaissance
     *
     * @param string $lieunaissance
     *
     * @return Infocomplementaire
     */
    public function setLieunaissance($lieunaissance)
    {
        $this->lieunaissance = $lieunaissance;

        return $this;
    }

    /**
     * Get lieunaissance
     *
     * @return string
     */
    public function getLieunaissance()
    {
        return $this->lieunaissance;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Infocomplementaire
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="langue1", type="string", length=45, nullable=true)
     */
    private $langue1;

    /**
     * @var string
     *
     * @ORM\Column(name="langue2", type="string", length=45, nullable=true)
     */
    private $langue2;
    /**
     * @var string
     *
     * @ORM\Column(name="langue3", type="string", length=45, nullable=true)
     */
    private $langue3;
    /**
     * @var string
     *
     * @ORM\Column(name="langue4", type="string", length=45, nullable=true)
     */
    private $langue4;
    /**
     * @var string
     *
     * @ORM\Column(name="langue5", type="string", length=45, nullable=true)
     */
    private $langue5;
    /**
     * @var string
     *
     * @ORM\Column(name="hobbie1", type="string", length=45, nullable=true)
     */
    private $hobbie1;
    /**
     * @var string
     *
     * @ORM\Column(name="hobbie2", type="string", length=45, nullable=true)
     */
    private $hobbie2;
    /**
     * @var string
     *
     * @ORM\Column(name="hobbie3", type="string", length=45, nullable=true)
     */
    private $hobbie3;
    /**
     * @var string
     *
     * @ORM\Column(name="hobbie4", type="string", length=45, nullable=true)
     */
    private $hobbie4;
    /**
     * @var string
     *
     * @ORM\Column(name="hobbie5", type="string", length=45, nullable=true)
     */
    private $hobbie5;

    /**
     * @var string
     *
     * @ORM\Column(name="competence1", type="string", length=45, nullable=true)
     */
    private $competence1;

    /**
     * @var string
     *
     * @ORM\Column(name="competence2", type="string", length=45, nullable=true)
     */
    private $competence2;

    /**
     * @var string
     *
     * @ORM\Column(name="competence3", type="string", length=45, nullable=true)
     */
    private $competence3;

    /**
     * @var string
     *
     * @ORM\Column(name="competence4", type="string", length=45, nullable=true)
     */
    private $competence4;

    /**
     * @var string
     *
     * @ORM\Column(name="competence5", type="string", length=45, nullable=true)
     */
    private $competence5;

    /**
     * Set mobilite
     *
     * @param integer $mobilite
     *
     * @return Infocomplementaire
     */
    public function setMobilite($mobilite)
    {
        $this->mobilite = $mobilite;

        return $this;
    }

    /**
     * Get mobilite
     *
     * @return integer
     */
    public function getMobilite()
    {
        return $this->mobilite;
    }

    /**
     * Set fratrie
     *
     * @param integer $fratrie
     *
     * @return Infocomplementaire
     */
    public function setFratrie($fratrie)
    {
        $this->fratrie = $fratrie;

        return $this;
    }

    /**
     * Get fratrie
     *
     * @return integer
     */
    public function getFratrie()
    {
        return $this->fratrie;
    }

    /**
     * Add villesFranceFreeVille
     *
     * @param \GenericBundle\Entity\VillesFranceFree $villesFranceFreeVille
     *
     * @return Infocomplementaire
     */
    public function addVillesFranceFreeVille(VillesFranceFree $villesFranceFreeVille)
    {
        $this->villesFranceFreeVille[] = $villesFranceFreeVille;

        return $this;
    }

    /**
     * Remove villesFranceFreeVille
     *
     * @param \GenericBundle\Entity\VillesFranceFree $villesFranceFreeVille
     */
    public function removeVillesFranceFreeVille(VillesFranceFree $villesFranceFreeVille)
    {
        $this->villesFranceFreeVille->removeElement($villesFranceFreeVille);
    }

    /**
     * Get villesFranceFreeVille
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVillesFranceFreeVille()
    {
        return $this->villesFranceFreeVille;
    }

    /**
     * Set permis
     *
     * @param boolean $permis
     *
     * @return Infocomplementaire
     */
    public function setPermis($permis)
    {
        $this->permis = $permis;

        return $this;
    }

    /**
     * Get permis
     *
     * @return boolean
     */
    public function getPermis()
    {
        return $this->permis;
    }

    /**
     * Set vehicule
     *
     * @param boolean $vehicule
     *
     * @return Infocomplementaire
     */
    public function setVehicule($vehicule)
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    /**
     * Get vehicule
     *
     * @return boolean
     */
    public function getVehicule()
    {
        return $this->vehicule;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Infocomplementaire
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set daterecup
     *
     * @param \DateTime $daterecup
     *
     * @return Infocomplementaire
     */
    public function setDaterecup($daterecup)
    {
        $this->daterecup = $daterecup;

        return $this;
    }

    /**
     * Get daterecup
     *
     * @return \DateTime
     */
    public function getDaterecup()
    {
        return $this->daterecup;
    }

    /**
     * Set datecreation
     *
     * @param \DateTime $datecreation
     *
     * @return Infocomplementaire
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
     * @return Infocomplementaire
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
     * Set profilcomplet
     *
     * @param int $profilcomplet
     *
     * @return Infocomplementaire
     */
    public function setProfilcomplet($profilcomplet)
    {
        $this->profilcomplet = $profilcomplet;

        return $this;
    }

    /**
     * Get profilcomplet
     *
     * @return int
     */
    public function getProfilcomplet()
    {
        return $this->profilcomplet;
    }

    /**
     * Set portable
     *
     * @param string $portable
     *
     * @return Infocomplementaire
     */
    public function setPortable($portable)
    {
        $this->portable = $portable;

        return $this;
    }

    /**
     * Get portable
     *
     * @return string
     */
    public function getPortable()
    {
        return $this->portable;
    }

    /**
     * Set handicape
     *
     * @param boolean $handicape
     *
     * @return Infocomplementaire
     */
    public function setHandicape($handicape)
    {
        $this->handicape = $handicape;

        return $this;
    }

    /**
     * Get handicape
     *
     * @return boolean
     */
    public function getHandicape()
    {
        return $this->handicape;
    }

    /**
     * Set entrepreneur
     *
     * @param boolean $entrepreneur
     *
     * @return Infocomplementaire
     */
    public function setEntrepreneur($entrepreneur)
    {
        $this->entrepreneur = $entrepreneur;

        return $this;
    }

    /**
     * Get entrepreneur
     *
     * @return boolean
     */
    public function getEntrepreneur()
    {
        return $this->entrepreneur;
    }

    /**
     * Set formationactuelle
     *
     * @param string $formationactuelle
     *
     * @return Infocomplementaire
     */
    public function setFormationactuelle($formationactuelle)
    {
        $this->formationactuelle = $formationactuelle;

        return $this;
    }

    /**
     * Get formationactuelle
     *
     * @return string
     */
    public function getFormationactuelle()
    {
        return $this->formationactuelle;
    }

    /**
     * Set dernierDiplome
     *
     * @param string $dernierDiplome
     *
     * @return Infocomplementaire
     */
    public function setDernierDiplome($dernierDiplome)
    {
        $this->dernierDiplome = $dernierDiplome;

        return $this;
    }

    /**
     * Get dernierDiplome
     *
     * @return string
     */
    public function getDernierDiplome()
    {
        return $this->dernierDiplome;
    }

    /**
     * Set langue1
     *
     * @param string $langue1
     *
     * @return Infocomplementaire
     */
    public function setLangue1($langue1)
    {
        $this->langue1 = $langue1;

        return $this;
    }

    /**
     * Get langue1
     *
     * @return string
     */
    public function getLangue1()
    {
        return $this->langue1;
    }

    /**
     * Set langue2
     *
     * @param string $langue2
     *
     * @return Infocomplementaire
     */
    public function setLangue2($langue2)
    {
        $this->langue2 = $langue2;

        return $this;
    }

    /**
     * Get langue2
     *
     * @return string
     */
    public function getLangue2()
    {
        return $this->langue2;
    }

    /**
     * Set langue3
     *
     * @param string $langue3
     *
     * @return Infocomplementaire
     */
    public function setLangue3($langue3)
    {
        $this->langue3 = $langue3;

        return $this;
    }

    /**
     * Get langue3
     *
     * @return string
     */
    public function getLangue3()
    {
        return $this->langue3;
    }

    /**
     * Set langue4
     *
     * @param string $langue4
     *
     * @return Infocomplementaire
     */
    public function setLangue4($langue4)
    {
        $this->langue4 = $langue4;

        return $this;
    }

    /**
     * Get langue4
     *
     * @return string
     */
    public function getLangue4()
    {
        return $this->langue4;
    }

    /**
     * Set langue5
     *
     * @param string $langue5
     *
     * @return Infocomplementaire
     */
    public function setLangue5($langue5)
    {
        $this->langue5 = $langue5;

        return $this;
    }

    /**
     * Get langue5
     *
     * @return string
     */
    public function getLangue5()
    {
        return $this->langue5;
    }

    /**
     * Set hobbie1
     *
     * @param string $hobbie1
     *
     * @return Infocomplementaire
     */
    public function setHobbie1($hobbie1)
    {
        $this->hobbie1 = $hobbie1;

        return $this;
    }

    /**
     * Get hobbie1
     *
     * @return string
     */
    public function getHobbie1()
    {
        return $this->hobbie1;
    }

    /**
     * Set hobbie2
     *
     * @param string $hobbie2
     *
     * @return Infocomplementaire
     */
    public function setHobbie2($hobbie2)
    {
        $this->hobbie2 = $hobbie2;

        return $this;
    }

    /**
     * Get hobbie2
     *
     * @return string
     */
    public function getHobbie2()
    {
        return $this->hobbie2;
    }

    /**
     * Set hobbie3
     *
     * @param string $hobbie3
     *
     * @return Infocomplementaire
     */
    public function setHobbie3($hobbie3)
    {
        $this->hobbie3 = $hobbie3;

        return $this;
    }

    /**
     * Get hobbie3
     *
     * @return string
     */
    public function getHobbie3()
    {
        return $this->hobbie3;
    }

    /**
     * Set hobbie4
     *
     * @param string $hobbie4
     *
     * @return Infocomplementaire
     */
    public function setHobbie4($hobbie4)
    {
        $this->hobbie4 = $hobbie4;

        return $this;
    }

    /**
     * Get hobbie4
     *
     * @return string
     */
    public function getHobbie4()
    {
        return $this->hobbie4;
    }

    /**
     * Set hobbie5
     *
     * @param string $hobbie5
     *
     * @return Infocomplementaire
     */
    public function setHobbie5($hobbie5)
    {
        $this->hobbie5 = $hobbie5;

        return $this;
    }

    /**
     * Get hobbie5
     *
     * @return string
     */
    public function getHobbie5()
    {
        return $this->hobbie5;
    }

    /**
     * Set lienexterne1
     *
     * @param string $lienexterne1
     *
     * @return Infocomplementaire
     */
    public function setLienexterne1($lienexterne1)
    {
        $this->Lienexterne1 = $lienexterne1;

        return $this;
    }

    /**
     * Get lienexterne1
     *
     * @return string
     */
    public function getLienexterne1()
    {
        return $this->Lienexterne1;
    }

    /**
     * Set lienexterne2
     *
     * @param string $lienexterne2
     *
     * @return Infocomplementaire
     */
    public function setLienexterne2($lienexterne2)
    {
        $this->Lienexterne2 = $lienexterne2;

        return $this;
    }

    /**
     * Get lienexterne2
     *
     * @return string
     */
    public function getLienexterne2()
    {
        return $this->Lienexterne2;
    }

    /**
     * Set lienexterne3
     *
     * @param string $lienexterne3
     *
     * @return Infocomplementaire
     */
    public function setLienexterne3($lienexterne3)
    {
        $this->Lienexterne3 = $lienexterne3;

        return $this;
    }

    /**
     * Get lienexterne3
     *
     * @return string
     */
    public function getLienexterne3()
    {
        return $this->Lienexterne3;
    }

    /**
     * Set competence1
     *
     * @param string $competence1
     *
     * @return Infocomplementaire
     */
    public function setCompetence1($competence1)
    {
        $this->competence1 = $competence1;

        return $this;
    }

    /**
     * Get competence1
     *
     * @return string
     */
    public function getCompetence1()
    {
        return $this->competence1;
    }

    /**
     * Set competence2
     *
     * @param string $competence2
     *
     * @return Infocomplementaire
     */
    public function setCompetence2($competence2)
    {
        $this->competence2 = $competence2;

        return $this;
    }

    /**
     * Get competence2
     *
     * @return string
     */
    public function getCompetence2()
    {
        return $this->competence2;
    }

    /**
     * Set competence3
     *
     * @param string $competence3
     *
     * @return Infocomplementaire
     */
    public function setCompetence3($competence3)
    {
        $this->competence3 = $competence3;

        return $this;
    }

    /**
     * Get competence3
     *
     * @return string
     */
    public function getCompetence3()
    {
        return $this->competence3;
    }

    /**
     * Set competence4
     *
     * @param string $competence4
     *
     * @return Infocomplementaire
     */
    public function setCompetence4($competence4)
    {
        $this->competence4 = $competence4;

        return $this;
    }

    /**
     * Get competence4
     *
     * @return string
     */
    public function getCompetence4()
    {
        return $this->competence4;
    }

    /**
     * Set competence5
     *
     * @param string $competence5
     *
     * @return Infocomplementaire
     */
    public function setCompetence5($competence5)
    {
        $this->competence5 = $competence5;

        return $this;
    }

    /**
     * Get competence5
     *
     * @return string
     */
    public function getCompetence5()
    {
        return $this->competence5;
    }
}
