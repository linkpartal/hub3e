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
     * @ORM\Column(name="nomJeuneFille", type="string", length=45, nullable=true)
     */
    private $nomjeunefille;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="statut", type="string", length=45, nullable=true)
     */
    private $statut;

    /**
     * @var \GenericBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \GenericBundle\Entity\ImportCandidat
     *
     * @ORM\ManyToOne(targetEntity="ImportCandidat", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id")
     * })
     */
    private $importCandidat;



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
     * Set statut
     *
     * @param string $statut
     *
     * @return Parents
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Parents
     */
    public function setUser(User $user = null)
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
     * Set importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     *
     * @return Parents
     */
    public function setImportCandidat(ImportCandidat $importCandidat = null)
    {
        $this->importCandidat = $importCandidat;

        return $this;
    }

    /**
     * Get importCandidat
     *
     * @return \GenericBundle\Entity\ImportCandidat
     */
    public function getImportCandidat()
    {
        return $this->importCandidat;
    }

    public function isEqual(Parents $parents)
    {
        if($parents->getNom()==$this->getNom() and $parents->getEmail()==$this->getEmail()
            and $parents->getTelephone()==$this->getTelephone() and $parents->getProfession()==$this->getProfession())
        {
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Set civilite
     *
     * @param string $civilite
     *
     * @return Parents
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
}
