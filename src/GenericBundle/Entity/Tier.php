<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tier
 *
 * @ORM\Table(name="tier", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_19653DBDDB8BBA08", columns={"siren"})})
 * @ORM\Entity(repositoryClass="GenericBundle\Repository\TierRepository")
 */
class Tier
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
     * @ORM\Column(name="siren", type="string", length=45, nullable=false)
     */
    private $siren;

    /**
     * @var string
     *
     * @ORM\Column(name="axe", type="string", length=45, nullable=true)
     */
    private $axe;

    /**
     * @var string
     *
     * @ORM\Column(name="avantage", type="string", length=45, nullable=true)
     */
    private $avantage;

    /**
     * @var string
     *
     * @ORM\Column(name="raisonSoc", type="string", length=45, nullable=true)
     */
    private $raisonsoc;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="blob", nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="fondEcran", type="blob", nullable=true)
     */
    private $fondecran;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ecole", type="boolean", nullable=false)
     */
    private $ecole;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Tier", inversedBy="tier", cascade={"remove"})
     * @ORM\JoinTable(name="tier_has_tier",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tier_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="tier_id1", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $tier1;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tier1 = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set siren
     *
     * @param string $siren
     *
     * @return Tier
     */
    public function setSiren($siren)
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get siren
     *
     * @return string
     */
    public function getSiren()
    {
        return $this->siren;
    }

    /**
     * Set raisonsoc
     *
     * @param string $raisonsoc
     *
     * @return Tier
     */
    public function setRaisonsoc($raisonsoc)
    {
        $this->raisonsoc = $raisonsoc;

        return $this;
    }

    /**
     * Get raisonsoc
     *
     * @return string
     */
    public function getRaisonsoc()
    {
        return $this->raisonsoc;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Tier
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set fondecran
     *
     * @param string $fondecran
     *
     * @return Tier
     */
    public function setFondecran($fondecran)
    {
        $this->fondecran = $fondecran;

        return $this;
    }

    /**
     * Get fondecran
     *
     * @return string
     */
    public function getFondecran()
    {
        return $this->fondecran;
    }

    /**
     * Set ecole
     *
     * @param boolean $ecole
     *
     * @return Tier
     */
    public function setEcole($ecole)
    {
        $this->ecole = $ecole;

        return $this;
    }

    /**
     * Get ecole
     *
     * @return boolean
     */
    public function getEcole()
    {
        return $this->ecole;
    }

    /**
     * Add tier1
     *
     * @param \GenericBundle\Entity\Tier $tier1
     *
     * @return Tier
     */
    public function addTier1(\GenericBundle\Entity\Tier $tier1)
    {
        $this->tier1[] = $tier1;

        return $this;
    }

    /**
     * Remove tier1
     *
     * @param \GenericBundle\Entity\Tier $tier1
     */
    public function removeTier1(\GenericBundle\Entity\Tier $tier1)
    {
        $this->tier1->removeElement($tier1);
    }

    /**
     * Get tier1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTier1()
    {
        return $this->tier1;
    }

    /**
     * Set axe
     *
     * @param string $axe
     *
     * @return Tier
     */
    public function setAxe($axe)
    {
        $this->axe = $axe;

        return $this;
    }

    /**
     * Get axe
     *
     * @return string
     */
    public function getAxe()
    {
        return $this->axe;
    }

    /**
     * Set avantage
     *
     * @param string $avantage
     *
     * @return Tier
     */
    public function setAvantage($avantage)
    {
        $this->avantage = $avantage;

        return $this;
    }

    /**
     * Get avantage
     *
     * @return string
     */
    public function getAvantage()
    {
        return $this->avantage;
    }
}
