<?php

namespace Mindgruve\Gordo\Tests\Entity;

/**
 * @Entity
 * @Proxy(target="Mindgruve\Gordo\Tests\Entity\TestProxy3",syncMethods={"*"},syncProperties={"field1"})
 */
class TestEntity3
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=140, name="message") */
    protected $field1;

    /** @Column(type="datetime", name="date") */
    protected $field2;

    /** @Column(length=140, name="email") */
    protected $field3;

    public function getId()
    {
        return $this->id;
    }

    public function getField1()
    {
        return $this->field1;
    }

    public function setField1($field1)
    {
        $this->field1 = $field1;
    }

    public function getField2()
    {
        return $this->field2;
    }

    public function setField2($field2)
    {
        $this->field2 = $field2;
    }

    public function getField3()
    {
        return $this->field3;
    }

    public function setField3($field3)
    {
        $this->field3 = $field3;
    }

}