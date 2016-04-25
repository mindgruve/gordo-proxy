<?php

namespace Mindgruve\Gordo\Tests\Entity;

/**
 * @Entity
 * @EntityProxy(target="Mindgruve\Gordo\Tests\Entity\TestProxy1")
 */
class TestEntity1
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140, name="message") */
    protected $field1;

    /** @Column(type="datetime", name="date") */
    protected $field2;

    /** @Column(length=140, name="email") */
    protected $field3;

}