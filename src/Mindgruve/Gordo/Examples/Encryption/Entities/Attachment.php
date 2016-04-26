<?php

namespace Mindgruve\Gordo\Examples\Encryption\Entities;

/**
 * @Entity
 * @Proxy(target="Mindgruve\Gordo\Examples\Encryption\Proxies\AttachmentProxy", sync="auto", syncProperties={"*"}, syncMethods={"*"})
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