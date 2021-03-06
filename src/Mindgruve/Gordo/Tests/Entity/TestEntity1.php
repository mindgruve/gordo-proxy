<?php

namespace Mindgruve\Gordo\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Proxy(target="Mindgruve\Gordo\Tests\Entity\TestProxy1")
 */
class TestEntity1
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

    /**
     * @OneToOne(targetEntity="TestAssociation1")
     * @JoinColumn(name="association1_id", referencedColumnName="id")
     */
    protected $association1;

    /**
     * @ManytoOne(targetEntity="TestAssociation1")
     * @JoinColumn(name="association2_id", referencedColumnName="id")
     */
    protected $association2;

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

    public function __construct()
    {
        $this->association2 = new ArrayCollection();
    }

    public function setAssociation1(TestAssociation1 $association1)
    {
        $this->association1 = $association1;
    }

    public function getAssociation1()
    {
        return $this->association1;
    }

    public function addAssociation2(TestAssociation1 $association)
    {
        $this->association2->add($association);
    }

    public function removeAssociation2(TestAssociation1 $association)
    {
        $this->association2->remove($association);
    }

    public function setAssociation2(ArrayCollection  $association2)
    {
        $this->association2 = $association2;
    }

    public function getAssociation2(){
        return $this->association2;
    }
}