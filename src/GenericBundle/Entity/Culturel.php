<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Culturel
 *
 * @ORM\Table(name="culturel", uniqueConstraints={@ORM\UniqueConstraint(name="culturel_UNIQUE", columns={"culturel"})})
 * @ORM\Entity
 */
class Culturel
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
     * @ORM\Column(name="culturel", type="string", length=45, nullable=false)
     */
    private $culturel;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="culturel")
     * @ORM\JoinTable(name="culturel_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="culturel_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\ImportCandidat", inversedBy="culturel")
     * @ORM\JoinTable(name="culturel_has_import_candidat",
     *   joinColumns={
     *     @ORM\JoinColumn(name="culturel_import_id", referencedColumnName="id")
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
     * Set culturel
     *
     * @param string $culturel
     *
     * @return Culturel
     */
    public function setCulturel($culturel)
    {
        $this->culturel = $culturel;

        return $this;
    }

    /**
     * Get culturel
     *
     * @return string
     */
    public function getCulturel()
    {
        return $this->culturel;
    }

    /**
     * Add user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Culturel
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
     * Add importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     *
     * @return Culturel
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
