<?php

namespace Mindgruve\Gordo\Tests;

/**
 * @Entity
 * @EntityProxy(target="Mindgruve\Gordo\Tests\TestProxy1")
 */
class TestEntity1
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

}