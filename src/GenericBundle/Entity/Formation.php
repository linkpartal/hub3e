<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formation
 *
 * @ORM\Table(name="formation", indexes={@ORM\Index(name="fk_Formation_ecole1_idx", columns={"etablissement_id"})})
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
     * @ORM\Column(name="Document", type="blob", length=65535, nullable=true)
     */
    private $document;

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
     * @ORM\ManyToMany(targetEntity="Qcmdef", mappedBy="formationformation")
     */
    private $qcmdef;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->qcmdef = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set document
     *
     * @param string $document
     *
     * @return Formation
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return Formation
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
     * Add qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     *
     * @return Formation
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
}
