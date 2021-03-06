<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImportCandidat
 *
 * @ORM\Table(name="import_candidat")
 * @ORM\Entity(repositoryClass="GenericBundle\Repository\ImportCandidatRepository")
 */
class ImportCandidat
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="civilite", type="string", length=20)
     */
    private $civilite;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="photos", type="blob", nullable=true)
     */
    private $photos;

    /**
     * @var string
     *
     * @ORM\Column(name="erreur", type="string", length=45, nullable=true)
     */
    private $erreur;

    /**
     * @var \GenericBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="GenericBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \GenericBundle\Entity\Etablissement
     *
     * @ORM\ManyToOne(targetEntity="GenericBundle\Entity\Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etablissement_id", referencedColumnName="id")
     * })
     */
    private $etablissement;

    /**
     * @var \GenericBundle\Entity\Infocomplementaire
     *
     * @ORM\ManyToOne(targetEntity="GenericBundle\Entity\Infocomplementaire",cascade={"persist","remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="infocomplementaire_import_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $info;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\Hobbies", mappedBy="importCandidat")
     */
    private $hobbies;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\Langue", mappedBy="importCandidat")
     */
    private $langue;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hobbies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->langue = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set civilite
     *
     * @param string $civilite
     *
     * @return ImportCandidat
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
     * @return ImportCandidat
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
     * @return ImportCandidat
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
     * @return ImportCandidat
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
     * Set email
     *
     * @param string $email
     *
     * @return ImportCandidat
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
     * Set erreur
     *
     * @param string $erreur
     *
     * @return ImportCandidat
     */
    public function setErreur($erreur)
    {
        $this->erreur = $erreur;

        return $this;
    }

    /**
     * Get erreur
     *
     * @return string
     */
    public function getErreur()
    {
        return $this->erreur;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return ImportCandidat
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

    /**
     * Set etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return ImportCandidat
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
     * Set info
     *
     * @param \GenericBundle\Entity\Infocomplementaire $info
     *
     * @return ImportCandidat
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
     * Add hobby
     *
     * @param \GenericBundle\Entity\Hobbies $hobby
     *
     * @return ImportCandidat
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
     * Add langue
     *
     * @param \GenericBundle\Entity\Langue $langue
     *
     * @return ImportCandidat
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
     * Set photos
     *
     * @param string $photos
     *
     * @return ImportCandidat
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
}
