<?php
// src/AppBundle/Entity/User.php

namespace GenericBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\Column;

/**
 * @ORM\Entity(repositoryClass="GenericBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 *@AttributeOverrides({
 *      @AttributeOverride(name="email",
 *          column=@Column(
 *              unique   = false
 *          )
 *      ),
 *      @AttributeOverride(name="emailCanonical",
 *          column=@Column(
 *              unique   = false
 *          )
 *      )
 * })
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=45, nullable=true)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="photos", type="blob", nullable=true)
     */
    private $photos;

    /**
     * @var \Tier
     *
     * @ORM\ManyToOne(targetEntity="Tier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tier_id", referencedColumnName="id")
     * })
     */
    private $tier;

    /**
     * @var \Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etablissement_id", referencedColumnName="id")
     * })
     */
    private $etablissement;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Culturel", mappedBy="users")
     */
    private $culturel;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Etablissement", mappedBy="users")
     */
    private $referenciel;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Mission", mappedBy="users")
     */
    private $mission;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Sport", mappedBy="users")
     */
    private $sport;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\Langue", mappedBy="users")
     */
    private $langue;

    /**
     * @var \Infocomplementaire
     *
     * @ORM\ManyToOne(targetEntity="Infocomplementaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="info_id", referencedColumnName="id",unique=true,onDelete="CASCADE")
     * })
     */
    private $info;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->culturel = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mission = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sport = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
     * Set tier
     *
     * @param \GenericBundle\Entity\Tier $tier
     *
     * @return User
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

    /**
     * Set etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return User
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

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return User
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getExpiredAt()
    {
        return $this->expiresAt;
    }



    /**
     * Add culturel
     *
     * @param \GenericBundle\Entity\Culturel $culturel
     *
     * @return User
     */
    public function addCulturel(\GenericBundle\Entity\Culturel $culturel)
    {
        $this->culturel[] = $culturel;

        return $this;
    }

    /**
     * Remove culturel
     *
     * @param \GenericBundle\Entity\Culturel $culturel
     */
    public function removeCulturel(\GenericBundle\Entity\Culturel $culturel)
    {
        $this->culturel->removeElement($culturel);
    }

    /**
     * Get culturel
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCulturel()
    {
        return $this->culturel;
    }

    /**
     * Add mission
     *
     * @param \GenericBundle\Entity\Mission $mission
     *
     * @return User
     */
    public function addMission(\GenericBundle\Entity\Mission $mission)
    {
        $this->mission[] = $mission;

        return $this;
    }

    /**
     * Remove mission
     *
     * @param \GenericBundle\Entity\Mission $mission
     */
    public function removeMission(\GenericBundle\Entity\Mission $mission)
    {
        $this->mission->removeElement($mission);
    }

    /**
     * Get mission
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Add sport
     *
     * @param \GenericBundle\Entity\Sport $sport
     *
     * @return User
     */
    public function addSport(\GenericBundle\Entity\Sport $sport)
    {
        $this->sport[] = $sport;

        return $this;
    }

    /**
     * Remove sport
     *
     * @param \GenericBundle\Entity\Sport $sport
     */
    public function removeSport(\GenericBundle\Entity\Sport $sport)
    {
        $this->sport->removeElement($sport);
    }

    /**
     * Get sport
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Set photos
     *
     * @param string $photos
     *
     * @return User
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photos
     *
     * @return string
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set info
     *
     * @param \GenericBundle\Entity\Infocomplementaire $info
     *
     * @return User
     */
    public function setInfo(\GenericBundle\Entity\Infocomplementaire $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \GenericBundle\Entity\Infocomplementaire
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Add referenciel
     *
     * @param \GenericBundle\Entity\Etablissement $referenciel
     *
     * @return User
     */
    public function addReferenciel(\GenericBundle\Entity\Etablissement $referenciel)
    {
        $this->referenciel[] = $referenciel;

        return $this;
    }

    /**
     * Remove referenciel
     *
     * @param \GenericBundle\Entity\Etablissement $referenciel
     */
    public function removeReferenciel(\GenericBundle\Entity\Etablissement $referenciel)
    {
        $this->referenciel->removeElement($referenciel);
    }

    /**
     * Get referenciel
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReferenciel()
    {
        return $this->referenciel;
    }

    /**
     * Add langue
     *
     * @param \GenericBundle\Entity\Langue $langue
     *
     * @return User
     */
    public function addLangue(\GenericBundle\Entity\Langue $langue)
    {
        $this->langue[] = $langue;

        return $this;
    }

    /**
     * Remove langue
     *
     * @param \GenericBundle\Entity\Langue $langue
     */
    public function removeLangue(\GenericBundle\Entity\Langue $langue)
    {
        $this->langue->removeElement($langue);
    }

    /**
     * Get langue
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLangue()
    {
        return $this->langue;
    }
}
