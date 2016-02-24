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
     * @ORM\Column(name="Reponse", type="string", length=255, nullable=false)
     */
    private $reponse;

    /**
     * @var string
     *
     * @ORM\Column(name="Ordre", type="string", length=45, nullable=true)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="Score", type="string", length=45, nullable=true)
     */

    private $score;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="reponsedef")
     * @ORM\JoinTable(name="reponsedef_has_users",
     *   joinColumns={
     *     @ORM\JoinColumn(name="reponsedef_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="users_id", referencedColumnName="id")
     *   }
     * )
     */
    private $users;


    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set score
     *
     * @param string $score
     *
     * @return Reponsedef
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
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

    /**
     * Add user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Reponsedef
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
}
