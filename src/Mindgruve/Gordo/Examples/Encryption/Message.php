<?php

namespace Mindgruve\Gordo\Examples\Encryption;

use Doctrine\Common\Collections\ArrayCollection;
use Mindgruve\Gordo\Annotations as DomainModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @DomainModel(domainModel="Mindgruve\Gordo\Examples\Encryption\MessageModel")
 */

class Message
{

    /**
     * @ORM\Id @Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(length=140, name="message") */
    protected $message;

    /** @ORM\Column(type="datetime", name="date") */
    protected $date;

    /** @ORM\Column(length=140, name="email") */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Attachment")
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id")
     */
    protected $attachments;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param ArrayCollection $a
     */
    public function setAttachments(ArrayCollection $a){
        $this->attachments = $a;
    }

    /**
     * @return mixed
     */
    public function getAttachments(){
        return $this->attachments;
    }

}