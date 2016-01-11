<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponsedef
 *
 * @ORM\Table(name="reponsedef", indexes={@ORM\Index(name="fk_ReponseDef_QuestionDef1_idx", columns={"QuestionDef_id"})})
 * @ORM\Entity
 */
class Reponsedef
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
     * @ORM\Column(name="Reponse", type="string", length=45, nullable=false)
     */
    private $reponse;

    /**
     * @var string
     *
     * @ORM\Column(name="Ordre", type="string", length=45, nullable=true)
     */
    private $ordre;

    /**
     * @var \Questiondef
     *
     * @ORM\ManyToOne(targetEntity="Questiondef")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="QuestionDef_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $questiondef;



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
     * Set reponse
     *
     * @param string $reponse
     *
     * @return Reponsedef
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return string
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * Set ordre
     *
     * @param string $ordre
     *
     * @return Reponsedef
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return string
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set questiondef
     *
     * @param \GenericBundle\Entity\Questiondef $questiondef
     *
     * @return Reponsedef
     */
    public function setQuestiondef(\GenericBundle\Entity\Questiondef $questiondef = null)
    {
        $this->questiondef = $questiondef;

        return $this;
    }

    /**
     * Get questiondef
     *
     * @return \GenericBundle\Entity\Questiondef
     */
    public function getQuestiondef()
    {
        return $this->questiondef;
    }

    static function sort_reponses_by_order(\GenericBundle\Entity\Reponsedef $a,\GenericBundle\Entity\Reponsedef $b) {
        if($a->getOrdre() == $b->getOrdre()){ return 0 ; }
        return ($a->getOrdre()< $b->getOrdre()) ? -1 : 1;
    }
}
