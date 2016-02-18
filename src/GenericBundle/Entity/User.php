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
     * @ORM\ManyToMany(targetEntity="Hobbies", mappedBy="users")
     */
    private $hobbies;

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
        $this->hobbies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mission = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add hobby
     *
     * @param \GenericBundle\Entity\Hobbies $hobby
     *
     * @return User
     */
    public function addHobby(\GenericBundle\Entity\Hobbies $hobby)
    {
        $this->hobbies[] = $hobby;

        return $this;
    }

    /**
     * Remove hobby
     *
     * @param \GenericBundle\Entity\Hobbies $hobby
     */
    public function removeHobby(\GenericBundle\Entity\Hobbies $hobby)
    {
        $this->hobbies->removeElement($hobby);
    }

    /**
     * Get hobbies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHobbies()
    {
        return $this->hobbies;
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
}
