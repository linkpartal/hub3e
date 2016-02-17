<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Langue
 *
 * @ORM\Table(name="langue")
 * @ORM\Entity
 */
class Langue
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
     * @ORM\Column(name="langue", type="string", length=45, nullable=false)
     */
    private $langue;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=45, nullable=false)
     */
    private $niveau;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="langue")
     * @ORM\JoinTable(name="langue_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="langue_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     *   }
     * )
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\ImportCandidat", inversedBy="langue")
     * @ORM\JoinTable(name="langue_has_import_candidat",
     *   joinColumns={
     *     @ORM\JoinColumn(name="langue_import_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id")
     *   }
     * )
     */
    private $importCandidat;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->importCandidat = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set langue
     *
     * @param string $langue
     *
     * @return Langue
     */
    public function setLangue($langue)
    {
        $this->langue = $langue;

        return $this;
    }

    /**
     * Get langue
     *
     * @return string
     */
    public function getLangue()
    {
        return $this->langue;
    }

    /**
     * Add user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Langue
     */
    public function addUser(\GenericBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \GenericBundle\Entity\User $user
     */
    public function removeUser(\GenericBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     *
     * @return Langue
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Add importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     *
     * @return Langue
     */
    public function addImportCandidat(\GenericBundle\Entity\ImportCandidat $importCandidat)
    {
        $this->importCandidat[] = $importCandidat;

        return $this;
    }

    /**
     * Remove importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     */
    public function removeImportCandidat(\GenericBundle\Entity\ImportCandidat $importCandidat)
    {
        $this->importCandidat->removeElement($importCandidat);
    }

    /**
     * Get importCandidat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImportCandidat()
    {
        return $this->importCandidat;
    }
}
