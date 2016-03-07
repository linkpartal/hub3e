<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity
 */
class Document
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
     * @ORM\Column(name="type", type="string", length=45, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=45, nullable=false)
     */
    private $extension;

    /**
     * @var integer
     *
     * @ORM\Column(name="taille", type="integer", nullable=false)
     */
    private $taille;

    /**
     * @var string
     *
     * @ORM\Column(name="document", type="blob", nullable=false)
     */
    private $document;

    /**
     * @var \User
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
     * Set id
     *
     * @param integer $id
     *
     * @return Document
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set type
     *
     * @param string $type
     *
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Document
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set taille
     *
     * @param integer $taille
     *
     * @return Document
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * Get taille
     *
     * @return integer
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * Set document
     *
     * @param string $document
     *
     * @return Document
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set users
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Document
     */
    public function setUser(\GenericBundle\Entity\User $user)
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
     * @return Document
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function isEqual(Document $document)
    {
        if($document->getDocument()==$this->getDocument() and $document->getType()==$this->getType() and $document->getName()==$this->getName() and $document->getExtension()==$this->getExtension())
        {
            return true;
        }
        else{
            return false;
        }
    }
}
