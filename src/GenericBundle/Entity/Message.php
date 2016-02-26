<?php

namespace GenericBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="message", indexes={@ORM\Index(name="fk_user_has_user_user2_idx", columns={"user_id1"}), @ORM\Index(name="fk_user_has_user_user1_idx", columns={"user_id"})})
 * @ORM\Entity
 */
class Message
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="Message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var \User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id1", referencedColumnName="id")
     * })
     */
    private $user1;

    /**
     * @var \User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set user1
     *
     * @param \GenericBundle\Entity\User $user1
     *
     * @return Message
     */
    public function setUser1(\GenericBundle\Entity\User $user1)
    {
        $this->user1 = $user1;

        return $this;
    }

    /**
     * Get user1
     *
     * @return \GenericBundle\Entity\User
     */
    public function getUser1()
    {
        return $this->user1;
    }

    /**
     * Set user
     *
     * @param \GenericBundle\Entity\User $user
     *
     * @return Message
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
}
