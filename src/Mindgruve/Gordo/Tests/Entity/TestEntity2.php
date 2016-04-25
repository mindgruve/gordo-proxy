<?php

namespace Mindgruve\Gordo\Tests\Entity;

/**
 * @Entity
 * @EntityProxy(target="Mindgruve\Gordo\Tests\Proxy\TestProxy1",syncAuto=false,syncProperties={"field1"},syncListeners={"setField1"})
 */
class TestEntity2
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