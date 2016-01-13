<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licencedef
 *
 * @ORM\Table(name="licencedef", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1DAAE648A4D60759", columns={"libelle"})})
 * @ORM\Entity
 */
class Licencedef
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
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="duree", type="integer", nullable=false)
     */
    private $duree;

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
     * @return Licencedef
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
     * Set description
     *
     * @param string $description
     *
     * @return Licencedef
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
     * Set duree
     *
     * @param integer $duree
     *
     * @return Licencedef
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return integer
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set maxapp
     *
     * @param integer $maxapp
     *
     * @return Licencedef
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
     * @return Licencedef
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
