<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parent
 *
 * @ORM\Table(name="parent")
 * @ORM\Entity
 */
class Parents
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
     * @ORM\Column(name="nomJeuneFille", type="string", length=45, nullable=true)
     */
    private $nomjeunefille;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=45, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="portable", type="string", length=45, nullable=true)
     */
    private $portable;

    /**
     * @var string
     *
     * @ORM\Column(name="profession", type="string", length=45, nullable=true)
     */
    private $profession;

    /**
     * @var string
     *
     * @ORM\Column(name="metier", type="string", length=45, nullable=true)
     */
    private $metier;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $user;



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
     * Set nom
     *
     * @param string $nom
     *
     * @return Parents
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
     * @return Parents
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
     * Set nomjeunefille
     *
     * @param string $nomjeunefille
     *
     * @return Parents
     */
    public function setNomjeunefille($nomjeunefille)
    {
        $this->nomjeunefille = $nomjeunefille;

        return $this;
    }

    /**
     * Get nomjeunefille
     *
     * @return string
     */
    public function getNomjeunefille()
    {
        return $this->nomjeunefille;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Parents
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
     * Set email
     *
     * @param string $email
     *
     * @return Parents
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Parents
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
     * Set portable
     *
     * @param string $portable
     *
     * @return Parents
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
     * Set profession
     *
     * @param string $profession
     *
     * @return Parents
     */
    public function setProfession($profession)
    {
        $this->profession = $profession;

        return $this;
    }

    /**
     * Get profession
     *
     * @return string
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * Set metier
     *
     * @param string $metier
     *
     * @return Parents
     */
    public function setMetier($metier)
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * Get metier
     *
     * @return string
     */
    public function getMetier()
    {
        return $this->metier;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Parents
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
}
