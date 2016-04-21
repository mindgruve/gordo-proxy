<?php

namespace Mindgruve\Gordo\Examples\Encryption;

/**
 * @Entity
 * @DomainMapping(domainModel="Mindgruve\Gordo\Examples\Encryption\AttachmentModel")
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