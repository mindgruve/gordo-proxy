<?php

namespace Mindgruve\Gordo\Tests\Entity;

/**
 * @Entity
 * @Proxy(target="Mindgruve\Gordo\Tests\Entity\TestAssociationProxy1")
 */
class TestAssociation1
{

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /** @Column(length=140, name="message") */
    protected $field1;

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

}