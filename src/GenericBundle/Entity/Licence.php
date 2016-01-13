<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licence
 *
 * @ORM\Table(name="licence", indexes={@ORM\Index(name="fk_Licence_copy1_Societe1_idx", columns={"tier_id"})})
 * @ORM\Entity
 */
class Licence
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
     * @ORM\Column(name="Libelle", type="string", length=45, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="DateDebut", type="string", length=45, nullable=true)
     */
    private $datedebut;

    /**
     * @var string
     *
     * @ORM\Column(name="DateFin", type="string", length=45, nullable=true)
     */
    private $datefin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_app", type="integer", nullable=false)
     */
    private $maxapp;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_mission", type="integer", nullable=false)
     */
    private $maxmission;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Licence
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set datedebut
     *
     * @param string $datedebut
     *
     * @return Licence
     */
    public function setDatedebut($datedebut)
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    /**
     * Get datedebut
     *
     * @return string
     */
    public function getDatedebut()
    {
        return $this->datedebut;
    }

    /**
     * Set datefin
     *
     * @param string $datefin
     *
     * @return Licence
     */
    public function setDatefin($datefin)
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get datefin
     *
     * @return string
     */
    public function getDatefin()
    {
        return $this->datefin;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Licence
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
     * Set tier
     *
     * @param \GenericBundle\Entity\Tier $tier
     *
     * @return Licence
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
     * Set maxapp
     *
     * @param integer $maxapp
     *
     * @return Licence
     */
    public function setMaxapp($maxapp)
    {
        $this->maxapp = $maxapp;

        return $this;
    }

    /**
     * Get maxapp
     *
     * @return integer
     */
    public function getMaxapp()
    {
        return $this->maxapp;
    }

    /**
     * Set maxmission
     *
     * @param integer $maxmission
     *
     * @return Licence
     */
    public function setMaxmission($maxmission)
    {
        $this->maxmission = $maxmission;

        return $this;
    }

    /**
     * Get maxmission
     *
     * @return integer
     */
    public function getMaxmission()
    {
        return $this->maxmission;
    }
}
