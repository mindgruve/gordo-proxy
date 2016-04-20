<?php

namespace Mindgruve\Gordo\Examples\Encryption;

use Doctrine\ORM\Mapping as ORM;
use Mindgruve\Gordo\Domain\Annotations as Domain;

/**
 * @ORM\Entity
 * @Domain(domainModel="Mindgruve\Gordo\Examples\Encryption\MessageModel")
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