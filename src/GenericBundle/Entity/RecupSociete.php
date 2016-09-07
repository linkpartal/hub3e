<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * RecupSociete
 *
 * @ORM\Table(name="RecupSociete",uniqueConstraints={@UniqueConstraint(name="unique_ecole_societe", columns={"ecole","societe"})})
 * @ORM\Entity
 */
class RecupSociete
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
     * @var \GenericBundle\Entity\Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ecole", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $ecole;

    /**
     * @var \GenericBundle\Entity\Etablissement
     *
     * @ORM\ManyToOne(targetEntity="Etablissement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="societe", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $societe;



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
     * Set ecole
     *
     * @param \GenericBundle\Entity\Etablissement $ecole
     *
     * @return RecupSociete
     */
    public function setEcole(\GenericBundle\Entity\Etablissement $ecole = null)
    {
        $this->ecole = $ecole;

        return $this;
    }

    /**
     * Get ecole
     *
     * @return \GenericBundle\Entity\Etablissement
     */
    public function getEcole()
    {
        return $this->ecole;
    }

    /**
     * Set societe
     *
     * @param \GenericBundle\Entity\Etablissement $societe
     *
     * @return RecupSociete
     */
    public function setSociete(\GenericBundle\Entity\Etablissement $societe = null)
    {
        $this->societe = $societe;

        return $this;
    }

    /**
     * Get societe
     *
     * @return \GenericBundle\Entity\Etablissement
     */
    public function getSociete()
    {
        return $this->societe;
    }
}
