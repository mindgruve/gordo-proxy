<?php

namespace Poncho\Examples\Encryption;

/**
 * @Entity
 */
class Attachment
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140, name="filename") */
    protected $filename;

}