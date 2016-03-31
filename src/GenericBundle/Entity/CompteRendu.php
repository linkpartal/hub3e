<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompteRendu
 *
 * @ORM\Table(name="compte_rendu")
 * @ORM\Entity(repositoryClass="GenericBundle\Repository\CompteRenduRepository")
 */
class CompteRendu
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_rendu", type="text", nullable=false)
     */
    private $compterendu;

    /**
     * @var integer
     *
     * @ORM\Column(name="honorer", type="boolean", nullable=false)
     */
    private $honorer;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="auteur", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $auteur;

    /**
     * @var \RDV
     *
     * @ORM\ManyToOne(targetEntity="RDV")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rendezvous", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $rendezvous;

    /**
     * Get id
     *
     * @return int
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
     * @return CompteRendu
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
     * Set compterendu
     *
     * @param string $compterendu
     *
     * @return CompteRendu
     */
    public function setCompterendu($compterendu)
    {
        $this->compterendu = $compterendu;

        return $this;
    }

    /**
     * Get compterendu
     *
     * @return string
     */
    public function getCompterendu()
    {
        return $this->compterendu;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return CompteRendu
     */
    public function setHonorer($honorer)
    {
        $this->honorer = $honorer;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getHonorer()
    {
        return $this->honorer;
    }

    /**
     * Set auteur
     *
     * @param \GenericBundle\Entity\User $auteur
     *
     * @return CompteRendu
     */
    public function setAuteur(\GenericBundle\Entity\User $auteur = null)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * Get auteur
     *
     * @return \GenericBundle\Entity\User
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set rendezvous
     *
     * @param \GenericBundle\Entity\RDV $rendezvous
     *
     * @return CompteRendu
     */
    public function setRendezvous(\GenericBundle\Entity\RDV $rendezvous = null)
    {
        $this->rendezvous = $rendezvous;

        return $this;
    }

    /**
     * Get rendezvous
     *
     * @return \GenericBundle\Entity\RDV
     */
    public function getRendezvous()
    {
        return $this->rendezvous;
    }
}
