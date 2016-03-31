<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Experience
 *
 * @ORM\Table(name="experience", indexes={@ORM\Index(name="fk_Experience_users1_idx", columns={"users_id"})})
 * @ORM\Entity
 */
class Experience
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
     * @ORM\Column(name="nomSociete", type="string", length=45, nullable=true)
     */
    private $nomsociete;

    /**
     * @var string
     *
     * @ORM\Column(name="activite", type="string", length=45, nullable=true)
     */
    private $activite;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=45, nullable=true)
     */
    private $lieu;

    /**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=45, nullable=true)
     */
    private $poste;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbreAnnee", type="integer", nullable=true)
     */
    private $nbreannee;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=45, nullable=true)
     */
    private $description;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $user;

    /**
     * @var \GenericBundle\Entity\ImportCandidat
     *
     * @ORM\ManyToOne(targetEntity="GenericBundle\Entity\ImportCandidat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id", onDelete="CASCADE")
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
     * Set nomsociete
     *
     * @param string $nomsociete
     *
     * @return Experience
     */
    public function setNomsociete($nomsociete)
    {
        $this->nomsociete = $nomsociete;

        return $this;
    }

    /**
     * Get nomsociete
     *
     * @return string
     */
    public function getNomsociete()
    {
        return $this->nomsociete;
    }

    /**
     * Set activite
     *
     * @param string $activite
     *
     * @return Experience
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return string
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set lieu
     *
     * @param string $lieu
     *
     * @return Experience
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set poste
     *
     * @param string $poste
     *
     * @return Experience
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return string
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set nbreannee
     *
     * @param integer $nbreannee
     *
     * @return Experience
     */
    public function setNbreannee($nbreannee)
    {
        $this->nbreannee = $nbreannee;

        return $this;
    }

    /**
     * Get nbreannee
     *
     * @return integer
     */
    public function getNbreannee()
    {
        return $this->nbreannee;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Experience
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Experience
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
     * Set importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     *
     * @return Experience
     */
    public function setImportCandidat(\GenericBundle\Entity\ImportCandidat $importCandidat = null)
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

    public function isEqual(Experience $experience)
    {
        if($experience->getNomsociete()==$this->getNomsociete() and $experience->getPoste()==$this->getPoste()
            and $experience->getActivite()==$this->getActivite() and $experience->getDescription()==$this->getDescription())
        {
            return true;
        }
        else{
            return false;
        }
    }
}
