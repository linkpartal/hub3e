<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questiondef
 *
 * @ORM\Table(name="questiondef", indexes={@ORM\Index(name="fk_QuestionDef_QCMDef1_idx", columns={"QCMDef_id"})})
 * @ORM\Entity
 */
class Questiondef
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
     * @ORM\Column(name="Question", type="string", length=255, nullable=false)
     */
    private $question;

    /**
     * @var integer
     *
     * @ORM\Column(name="Ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var \GenericBundle\Entity\Qcmdef
     *
     * @ORM\ManyToOne(targetEntity="Qcmdef")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="QCMDef_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $qcmdef;



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
     * Set question
     *
     * @param string $question
     *
     * @return Questiondef
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return Questiondef
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set qcmdef
     *
     * @param \GenericBundle\Entity\Qcmdef $qcmdef
     *
     * @return Questiondef
     */
    public function setQcmdef(Qcmdef $qcmdef = null)
    {
        $this->qcmdef = $qcmdef;

        return $this;
    }

    /**
     * Get qcmdef
     *
     * @return \GenericBundle\Entity\Qcmdef
     */
    public function getQcmdef()
    {
        return $this->qcmdef;
    }

    static function sort_questions_by_order(Questiondef $a,Questiondef $b) {
        if($a->getOrdre() == $b->getOrdre()){ return 0 ; }
        return ($a->getOrdre()< $b->getOrdre()) ? -1 : 1;
    }
}
