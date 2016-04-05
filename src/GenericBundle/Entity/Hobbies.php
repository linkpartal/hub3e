<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Culturel
 *
 * @ORM\Table(name="hobbies", uniqueConstraints={@ORM\UniqueConstraint(name="hobbie_UNIQUE", columns={"hobbie"})})
 * @ORM\Entity
 */
class Hobbies
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
     * @ORM\Column(name="hobbie", type="string", length=45, nullable=false)
     */
    private $hobbie;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="hobbies",cascade={"remove"})
     * @ORM\JoinTable(name="hobbies_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="hobby_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="users_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ImportCandidat", inversedBy="hobbies",cascade={"remove"})
     * @ORM\JoinTable(name="hobbies_has_import_candidat",
     *   joinColumns={
     *     @ORM\JoinColumn(name="hobby_import_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $importCandidat;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->importCandidat = new ArrayCollection();
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

    public function addUser(User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \GenericBundle\Entity\User $user
     */
    public function removeUser(User $user)
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
     * @return Hobbies
     */
    public function addImportCandidat(ImportCandidat $importCandidat)
    {
        $this->importCandidat[] = $importCandidat;

        return $this;
    }

    /**
     * Remove importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     */
    public function removeImportCandidat(ImportCandidat $importCandidat)
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

    /**
     * Set hobbie
     *
     * @param string $hobbie
     *
     * @return Hobbies
     */
    public function setHobbie($hobbie)
    {
        $this->hobbie = $hobbie;

        return $this;
    }

    /**
     * Get hobbie
     *
     * @return string
     */
    public function getHobbie()
    {
        return $this->hobbie;
    }
}
