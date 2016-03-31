<?php



namespace GenericBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Etablissement
 *
 * @ORM\Table(name="etablissement", uniqueConstraints={@ORM\UniqueConstraint(name="SIRET_UNIQUE", columns={"SIRET"})}, indexes={@ORM\Index(name="fk_etablissement_societe1_idx", columns={"tier_id"})})
 * @ORM\Entity(repositoryClass="GenericBundle\Repository\EtablissementRepository")
 */
class Etablissement
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
     * @ORM\Column(name="SIRET", type="string", length=14, nullable=false)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=45, nullable=false)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codepostal", type="string", length=45, nullable=false)
     */
    private $codepostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=45, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=45, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="geocode", type="string", length=45, nullable=true)
     */
    private $geocode;

    /**
     * @var string
     *
     * @ORM\Column(name="nomResp", type="string", length=45, nullable=true)
     */
    private $nomResp;

    /**
     * @var string
     *
     * @ORM\Column(name="prenomResp", type="string", length=45, nullable=true)
     */
    private $prenomResp;

    /**
     * @var string
     *
     * @ORM\Column(name="telResponsable", type="string", length=45, nullable=true)
     */
    private $telresponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="mailResponsable", type="string", length=45, nullable=true)
     */
    private $mailresponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=45, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=45, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="taille", type="string", length=45, nullable=true)
     */
    private $taille;

    /**
     * @var string
     *
     * @ORM\Column(name="secteur", type="string", length=45, nullable=true)
     */
    private $secteur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="suspendu", type="boolean", nullable=true)
     */
    private $suspendu = false;

    /**
     * @var \Tier
     *
     * @ORM\ManyToOne(targetEntity="Tier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tier_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $tier;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Qcmdef", inversedBy="etablissement")
     * @ORM\JoinTable(name="etablissement_has_qcmdef",
     *   joinColumns={
     *     @ORM\JoinColumn(name="etablissement_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="qcmdef_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $qcmdef;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="etablissement", cascade={"remove"})
     * @ORM\JoinTable(name="referentiel",
     *   joinColumns={
     *     @ORM\JoinColumn(name="referentiel_etablissement_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="users_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->qcmdef = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set siret
     *
     * @param string $siret
     *
     * @return Etablissement
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Etablissement
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
     * Set codepostal
     *
     * @param string $codepostal
     *
     * @return Etablissement
     */
    public function setCodepostal($codepostal)
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    /**
     * Get codepostal
     *
     * @return string
     */
    public function getCodepostal()
    {
        return $this->codepostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Etablissement
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Etablissement
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
     * Set fax
     *
     * @param string $fax
     *
     * @return Etablissement
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set geocode
     *
     * @param string $geocode
     *
     * @return Etablissement
     */
    public function setGeocode($geocode)
    {
        $this->geocode = $geocode;

        return $this;
    }

    /**
     * Get geocode
     *
     * @return string
     */
    public function getGeocode()
    {
        return $this->geocode;
    }

    /**
     * Set telresponsable
     *
     * @param string $telresponsable
     *
     * @return Etablissement
     */
    public function setTelresponsable($telresponsable)
    {
        $this->telresponsable = $telresponsable;

        return $this;
    }

    /**
     * Get telresponsable
     *
     * @return string
     */
    public function getTelresponsable()
    {
        return $this->telresponsable;
    }

    /**
     * Set mailresponsable
     *
     * @param string $mailresponsable
     *
     * @return Etablissement
     */
    public function setMailresponsable($mailresponsable)
    {
        $this->mailresponsable = $mailresponsable;

        return $this;
    }

    /**
     * Get mailresponsable
     *
     * @return string
     */
    public function getMailresponsable()
    {
        return $this->mailresponsable;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return Etablissement
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set tier
     *
     * @param \GenericBundle\Entity\Tier $tier
     *
     * @return Etablissement
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
     * Add qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     *
     * @return Etablissement
     */
    public function addQcmdef(\GenericBundle\Entity\Qcmdef $qcmdef)
    {
        $this->qcmdef[] = $qcmdef;

        return $this;
    }

    /**
     * Remove qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     */
    public function removeQcmdef(\GenericBundle\Entity\Qcmdef $qcmdef)
    {
        $this->qcmdef->removeElement($qcmdef);
    }

    /**
     * Get qcmdef
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQcmdef()
    {
        return $this->qcmdef;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Etablissement
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set suspendu
     *
     * @param boolean $suspendu
     *
     * @return Etablissement
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
     * Add user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Etablissement
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

    public function __toString() {
        return $this->siret;
    }

    /**
     * Set nomResp
     *
     * @param string $nomResp
     *
     * @return Etablissement
     */
    public function setNomResp($nomResp)
    {
        $this->nomResp = $nomResp;

        return $this;
    }

    /**
     * Get nomResp
     *
     * @return string
     */
    public function getNomResp()
    {
        return $this->nomResp;
    }

    /**
     * Set prenomResp
     *
     * @param string $prenomResp
     *
     * @return Etablissement
     */
    public function setPrenomResp($prenomResp)
    {
        $this->prenomResp = $prenomResp;

        return $this;
    }

    /**
     * Get prenomResp
     *
     * @return string
     */
    public function getPrenomResp()
    {
        return $this->prenomResp;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Etablissement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set taille
     *
     * @param string $taille
     *
     * @return Etablissement
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille
     *
     * @return string
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * Set secteur
     *
     * @param string $secteur
     *
     * @return Etablissement
     */
    public function setSecteur($secteur)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return string
     */
    public function getSecteur()
    {
        return $this->secteur;
    }
}
