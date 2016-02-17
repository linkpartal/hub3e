<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="adresse", type="string", length=45, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codepostal", type="string", length=45, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=45, nullable=true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedin", type="string", length=45, nullable=true)
     */
    private $linkedin;

    /**
     * @var string
     *
     * @ORM\Column(name="viadeo", type="string", length=45, nullable=true)
     */
    private $viadeo;

    /**
     * @var integer
     *
     * @ORM\Column(name="mobilite", type="integer", nullable=true)
     */
    private $mobilite;

    /**
     * @var integer
     *
     * @ORM\Column(name="fratrie", type="integer", nullable=true)
     */
    private $fratrie;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permis", type="boolean", nullable=true)
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
        $this->villesFranceFreeVille = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set facebook
     *
     * @param string $facebook
     *
     * @return Infocomplementaire
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set linkedin
     *
     * @param string $linkedin
     *
     * @return Infocomplementaire
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    /**
     * Get linkedin
     *
     * @return string
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * Set viadeo
     *
     * @param string $viadeo
     *
     * @return Infocomplementaire
     */
    public function setViadeo($viadeo)
    {
        $this->viadeo = $viadeo;

        return $this;
    }

    /**
     * Get viadeo
     *
     * @return string
     */
    public function getViadeo()
    {
        return $this->viadeo;
    }

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
    public function addVillesFranceFreeVille(\GenericBundle\Entity\VillesFranceFree $villesFranceFreeVille)
    {
        $this->villesFranceFreeVille[] = $villesFranceFreeVille;

        return $this;
    }

    /**
     * Remove villesFranceFreeVille
     *
     * @param \GenericBundle\Entity\VillesFranceFree $villesFranceFreeVille
     */
    public function removeVillesFranceFreeVille(\GenericBundle\Entity\VillesFranceFree $villesFranceFreeVille)
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
}
