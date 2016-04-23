<?php

namespace Mindgruve\Gordo\Examples\Encryption\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @ProxyTransform(target="Mindgruve\Gordo\Examples\Encryption\Proxies\MessageProxy",syncListeners={"setDate"},syncAuto=true)
 */

class Message
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140, name="message") */
    protected $message;

    /** @Column(type="datetime", name="date") */
    protected $date;

    /** @Column(length=140, name="email") */
    protected $email;

    /**
     * @ManyToOne(targetEntity="Attachment")
     * @JoinColumn(name="attachment_id", referencedColumnName="id")
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