<?php

namespace Mindgruve\Gordo\Examples\Encryption\Entities;

/**
 * @Entity
 * @ProxyMapping(proxy="Mindgruve\Gordo\Examples\Encryption\Proxies\AttachmentProxy")
 */
class Attachment
{

    /**
     * @Id @Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @Column(length=140, name="filename") */
    protected $filename;

}