<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diplome
 *
 * @ORM\Table(name="diplome")
 * @ORM\Entity
 */
class Diplome
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="obtention", type="string", length=45, nullable=true)
     */
    private $obtention;

    /**
     * @var string
     *
     * @ORM\Column(name="ecole", type="string", length=45, nullable=true)
     */
    private $ecole;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * @return Diplome
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
     * Set obtention
     *
     * @param string $obtention
     *
     * @return Diplome
     */
    public function setObtention($obtention)
    {
        $this->obtention = $obtention;

        return $this;
    }

    /**
     * Get obtention
     *
     * @return string
     */
    public function getObtention()
    {
        return $this->obtention;
    }

    /**
     * Set ecole
     *
     * @param string $ecole
     *
     * @return Diplome
     */
    public function setEcole($ecole)
    {
        $this->ecole = $ecole;

        return $this;
    }

    /**
     * Get ecole
     *
     * @return string
     */
    public function getEcole()
    {
        return $this->ecole;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Diplome
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
}
