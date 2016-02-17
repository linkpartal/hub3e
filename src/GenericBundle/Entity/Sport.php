<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sport
 *
 * @ORM\Table(name="sport", uniqueConstraints={@ORM\UniqueConstraint(name="sport_UNIQUE", columns={"sport"})})
 * @ORM\Entity
 */
class Sport
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
     * @ORM\Column(name="sport", type="string", length=45, nullable=false)
     */
    private $sport;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="sport")
     * @ORM\JoinTable(name="sport_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="sport_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="GenericBundle\Entity\ImportCandidat", inversedBy="sport")
     * @ORM\JoinTable(name="sport_has_import_candidat",
     *   joinColumns={
     *     @ORM\JoinColumn(name="sport_import_id", referencedColumnName="id")
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
     * Set sport
     *
     * @param string $sport
     *
     * @return Sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return string
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Add user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Sport
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
     * @return Sport
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
