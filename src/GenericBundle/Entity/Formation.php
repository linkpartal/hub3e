<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Formation
 *
 * @ORM\Table(name="formation",uniqueConstraints={@UniqueConstraint(name="unique_formation_etablissement", columns={"nom","etablissement_id"})},
 *      indexes={@ORM\Index(name="fk_Formation_ecole1_idx", columns={"etablissement_id"})})
 * @ORM\Entity
 */
class Formation
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
     * @ORM\Column(name="Descriptif", type="string", length=45, nullable=true)
     */
    private $descriptif;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=45, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="NomDoc", type="string", length=45, nullable=false)
     */
    private $nomDoc;


    /**
     * @var string
     *
     * @ORM\Column(name="Metier1", type="string", length=100, nullable=true)
     */
    private $metier1;

    /**
     * @var string
     *
     * @ORM\Column(name="Metier2", type="string", length=100, nullable=true)
     */
    private $metier2;

    /**
     * @var string
     *
     * @ORM\Column(name="Metier3", type="string", length=100, nullable=true)
     */
    private $metier3;


    /**
     * @var string
     *
     * @ORM\Column(name="NomResponsable", type="string", length=100, nullable=true)
     */
    private $nomResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="PrenomResponsable", type="string", length=100, nullable=true)
     */
    private $prenomResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="TelResponsable", type="string", length=40, nullable=true)
     */
    private $telResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="MailResponsable", type="string", length=100, nullable=true)
     */
    private $mailResponsable;

    /**
     * @var string
     *
     * @ORM\Column(name="FonctionResponsable", type="string", length=100, nullable=true)
     */
    private $fonctionResponsable;




    /**
     * @var \GenericBundle\Entity\Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etablissement_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $etablissement;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Qcmdef", mappedBy="formation")
     */
    private $qcmdef;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->qcmdef = new ArrayCollection();
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
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Formation
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return Formation
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
     * Add qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     *
     * @return Formation
     */
    public function addQcmdef(Qcmdef $qcmdef)
    {
        $this->qcmdef[] = $qcmdef;

        return $this;
    }

    /**
     * Remove qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     */
    public function removeQcmdef(Qcmdef $qcmdef)
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Formation
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
     * Set nomDoc
     *
     * @param string $nomDoc
     *
     * @return Formation
     */
    public function setNomDoc($nomDoc)
    {
        $this->nomDoc = $nomDoc;

        return $this;
    }

    /**
     * Get nomDoc
     *
     * @return string
     */
    public function getNomDoc()
    {
        return $this->nomDoc;
    }

    /**
     * Set metier1
     *
     * @param string $metier1
     *
     * @return Formation
     */
    public function setMetier1($metier1)
    {
        $this->metier1 = $metier1;

        return $this;
    }

    /**
     * Get metier1
     *
     * @return string
     */
    public function getMetier1()
    {
        return $this->metier1;
    }

    /**
     * Set metier2
     *
     * @param string $metier2
     *
     * @return Formation
     */
    public function setMetier2($metier2)
    {
        $this->metier2 = $metier2;

        return $this;
    }

    /**
     * Get metier2
     *
     * @return string
     */
    public function getMetier2()
    {
        return $this->metier2;
    }

    /**
     * Set metier3
     *
     * @param string $metier3
     *
     * @return Formation
     */
    public function setMetier3($metier3)
    {
        $this->metier3 = $metier3;

        return $this;
    }

    /**
     * Get metier3
     *
     * @return string
     */
    public function getMetier3()
    {
        return $this->metier3;
    }

    /**
     * Set nomResponsable
     *
     * @param string $nomResponsable
     *
     * @return Formation
     */
    public function setNomResponsable($nomResponsable)
    {
        $this->nomResponsable = $nomResponsable;

        return $this;
    }

    /**
     * Get nomResponsable
     *
     * @return string
     */
    public function getNomResponsable()
    {
        return $this->nomResponsable;
    }

    /**
     * Set prenomResponsable
     *
     * @param string $prenomResponsable
     *
     * @return Formation
     */
    public function setPrenomResponsable($prenomResponsable)
    {
        $this->prenomResponsable = $prenomResponsable;

        return $this;
    }

    /**
     * Get prenomResponsable
     *
     * @return string
     */
    public function getPrenomResponsable()
    {
        return $this->prenomResponsable;
    }

    /**
     * Set telResponsable
     *
     * @param string $telResponsable
     *
     * @return Formation
     */
    public function setTelResponsable($telResponsable)
    {
        $this->telResponsable = $telResponsable;

        return $this;
    }

    /**
     * Get telResponsable
     *
     * @return string
     */
    public function getTelResponsable()
    {
        return $this->telResponsable;
    }

    /**
     * Set mailResponsable
     *
     * @param string $mailResponsable
     *
     * @return Formation
     */
    public function setMailResponsable($mailResponsable)
    {
        $this->mailResponsable = $mailResponsable;

        return $this;
    }

    /**
     * Get mailResponsable
     *
     * @return string
     */
    public function getMailResponsable()
    {
        return $this->mailResponsable;
    }

    /**
     * Set fonctionResponsable
     *
     * @param string $fonctionResponsable
     *
     * @return Formation
     */
    public function setFonctionResponsable($fonctionResponsable)
    {
        $this->fonctionResponsable = $fonctionResponsable;

        return $this;
    }

    /**
     * Get fonctionResponsable
     *
     * @return string
     */
    public function getFonctionResponsable()
    {
        return $this->fonctionResponsable;
    }
}
