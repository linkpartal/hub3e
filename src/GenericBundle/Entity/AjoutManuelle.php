<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AjoutManuelle
 *
 * @ORM\Table(name="ajout_manuelle")
 * @ORM\Entity
 */
class AjoutManuelle
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
     * @var \GenericBundle\Entity\Mission
     *
     * @ORM\ManyToOne(targetEntity="Mission")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mission", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $mission;


    /**
     * @var \GenericBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="apprenant", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $apprenant;
	
	
	  /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;
	


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return AjoutManuelle
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set mission
     *
     * @param \GenericBundle\Entity\Mission $mission
     *
     * @return AjoutManuelle
     */
    public function setMission(\GenericBundle\Entity\Mission $mission = null)
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * Get mission
     *
     * @return \GenericBundle\Entity\Mission
     */
    public function getMission()
    {
        return $this->mission;
    }

    /**
     * Set apprenant
     *
     * @param \GenericBundle\Entity\User $apprenant
     *
     * @return AjoutManuelle
     */
    public function setApprenant(\GenericBundle\Entity\User $apprenant = null)
    {
        $this->apprenant = $apprenant;

        return $this;
    }

    /**
     * Get apprenant
     *
     * @return \GenericBundle\Entity\User
     */
    public function getApprenant()
    {
        return $this->apprenant;
    }
}
