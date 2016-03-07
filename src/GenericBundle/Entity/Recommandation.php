<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recommandation
 *
 * @ORM\Table(name="recommandation", indexes={@ORM\Index(name="fk_Recommandation_users1_idx", columns={"users_id"})})
 * @ORM\Entity
 */
class Recommandation
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
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="fonction", type="string", length=45, nullable=true)
     */
    private $fonction;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

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
     * @var \GenericBundle\Entity\ImportCandidat
     *
     * @ORM\ManyToOne(targetEntity="GenericBundle\Entity\ImportCandidat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="import_candidat_id", referencedColumnName="id")
     * })
     */
    private $importCandidat;



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
     * Set text
     *
     * @param string $text
     *
     * @return Recommandation
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Recommandation
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
     * Set fonction
     *
     * @param string $fonction
     *
     * @return Recommandation
     */
    public function setFonction($fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get fonction
     *
     * @return string
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Recommandation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Recommandation
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Recommandation
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

    /**
     * Set importCandidat
     *
     * @param \GenericBundle\Entity\ImportCandidat $importCandidat
     *
     * @return Recommandation
     */
    public function setImportCandidat(\GenericBundle\Entity\ImportCandidat $importCandidat = null)
    {
        $this->importCandidat = $importCandidat;

        return $this;
    }

    /**
     * Get importCandidat
     *
     * @return \GenericBundle\Entity\ImportCandidat
     */
    public function getImportCandidat()
    {
        return $this->importCandidat;
    }

    public function isEqual(Recommandation $recommandation)
    {
        if($recommandation->getNom()==$this->getNom() and $recommandation->getEmail()==$this->getEmail()
            and $recommandation->getTelephone()==$this->getTelephone() and $recommandation->getFonction()==$this->getFonction())
        {
            return true;
        }
        else{
            return false;
        }
    }
}
