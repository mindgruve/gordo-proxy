<?php

namespace Mindgruve\Gordo\Examples\Encryption;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Attachment
{

    /**
     * @ORM\Id @Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /** @ORM\Column(length=140, name="filename") */
    protected $filename;

}