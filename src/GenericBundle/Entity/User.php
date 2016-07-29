<?php
// src/AppBundle/Entity/User.php

namespace GenericBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\Column;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(name="telephone", type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="photos", type="blob", nullable=true)
     */
    private $photos;

    /**
     * @var \GenericBundle\Entity\Tier
     *
     * @ORM\ManyToOne(targetEntity="Tier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tier_id", referencedColumnName="id")
     * })
     */
    private $tier;

    /**
     * @var \GenericBundle\Entity\Etablissement
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
     * @ORM\ManyToMany(targetEntity="Etablissement", mappedBy="users", cascade={"remove"})
     */
    private $referenciel;

    /**
     * @var \GenericBundle\Entity\Infocomplementaire
     *
     * @ORM\ManyToOne(targetEntity="Infocomplementaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="info_id", referencedColumnName="id",unique=true,onDelete="CASCADE")
     * })
     */
    private $info;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Reponsedef", mappedBy="users")
     */
    private $reponsedef;

    /**
     * @var integer
     *
     * @ORM\Column(name="place", type="integer", nullable=true)
     */
    private $place;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->referenciel = new ArrayCollection();
        $this->reponsedef = new ArrayCollection();
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
     * Set tier
     *
     * @param \GenericBundle\Entity\Tier $tier
     *
     * @return User
     */
    public function setTier(Tier $tier = null)
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
     * Add referenciel
     *
     * @param \GenericBundle\Entity\Etablissement $referenciel
     *
     * @return User
     */
    public function addReferenciel(Etablissement $referenciel)
    {
        $this->referenciel[] = $referenciel;

        return $this;
    }

    /**
     * Remove referenciel
     *
     * @param \GenericBundle\Entity\Etablissement $referenciel
     */
    public function removeReferenciel(Etablissement $referenciel)
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
     * Set info
     *
     * @param \GenericBundle\Entity\Infocomplementaire $info
     *
     * @return User
     */
    public function setInfo(Infocomplementaire $info = null)
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

    public function getExpiredAt()
    {
        $this->expiresAt;
    }

    /**
     * Add reponsedef
     *
     * @param \GenericBundle\Entity\Reponsedef $reponsedef
     *
     * @return User
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
     * Set place
     *
     * @param integer $pourvue
     *
     * @return Mission
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return integer
     */
    public function getPlace()
    {
        return $this->place;
    }
}
