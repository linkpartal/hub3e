<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Qcmdef
 *
 * @ORM\Table(name="qcmdef")
 * @ORM\Entity
 */
class Qcmdef
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
     * @ORM\Column(name="Nom", type="string", length=45, nullable=false, unique=true)
     */
    private $nom;

    /**
     * @var boolean
     *
     * @ORM\Column(name="affinite", type="boolean", nullable=false)
     */
    private $affinite= true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Etablissement", mappedBy="qcmdef")
     */
    private $etablissement;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Formation", inversedBy="qcmdef", cascade={"remove"})
     * @ORM\JoinTable(name="qcmdef_has_formation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="qcmdef_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="formation_idformation", referencedColumnName="id")
     *   }
     * )
     */
    private $formation;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etablissement = new \Doctrine\Common\Collections\ArrayCollection();
        $this->formation = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Qcmdef
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
     * Add etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     *
     * @return Qcmdef
     */
    public function addEtablissement(\GenericBundle\Entity\Etablissement $etablissement)
    {
        $this->etablissement[] = $etablissement;

        return $this;
    }

    /**
     * Remove etablissement
     *
     * @param \GenericBundle\Entity\Etablissement $etablissement
     */
    public function removeEtablissement(\GenericBundle\Entity\Etablissement $etablissement)
    {
        $this->etablissement->removeElement($etablissement);
    }

    /**
     * Get etablissement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEtablissement()
    {
        return $this->etablissement;
    }

    /**
     * Set affinite
     *
     * @param boolean $affinite
     *
     * @return Qcmdef
     */
    public function setAffinite($affinite)
    {
        $this->affinite = $affinite;

        return $this;
    }

    /**
     * Get affinite
     *
     * @return boolean
     */
    public function getAffinite()
    {
        return $this->affinite;
    }

    /**
     * Add formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     *
     * @return Qcmdef
     */
    public function addFormation(\GenericBundle\Entity\Formation $formation)
    {
        $this->formation[] = $formation;

        return $this;
    }

    /**
     * Remove formation
     *
     * @param \GenericBundle\Entity\Formation $formation
     */
    public function removeFormation(\GenericBundle\Entity\Formation $formation)
    {
        $this->formation->removeElement($formation);
    }

    /**
     * Get formation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFormation()
    {
        return $this->formation;
    }


    public function __toString() {
        return $this->nom;
    }
}
