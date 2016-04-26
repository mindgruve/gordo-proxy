<?php

namespace Mindgruve\Gordo\Examples\Encryption\Entities;

/**
 * @Entity
 * @EntityProxy(target="Mindgruve\Gordo\Examples\Encryption\Proxies\AttachmentProxy", syncAuto=true, syncProperties={"*"}, syncMethods={"*"})
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