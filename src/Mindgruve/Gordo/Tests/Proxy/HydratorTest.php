<?php

namespace Mindgruve\Gordo\Tests\Proxy;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Tests\Entity\TestEntity1;

class HydratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @var TestEntity1
     */
    protected $entity1;

    /**
     * Data Fixture SETUP
     */
    public function setup()
    {
        $entity1 = new TestEntity1();
        $entity1->setField1('a');
        $entity1->setField2('b');
        $entity1->setField3('c');
        $this->entity1 = $entity1;

        $hydrator = new Hydrator(get_class($entity1));
        $this->hydrator = $hydrator;
    }

    /**
     * Test ability to extract properties as an array
     */
    public function testExtract()
    {
        $this->assertEquals(
            array(
                'id'     => null,
                'field1' => 'a',
                'field2' => 'b',
                'field3' => 'c',
            ),
            $this->hydrator->extract($this->entity1)
        );
    }

    /**
     * Test ablity to hydrate object based off an array of values
     */
    public function testHydrate()
    {

        $this->hydrator->hydrate(
            array(
                'id'     => 1,
                'field1' => 'x',
                'field2' => 'y',
                'field3' => 'z'
            ),
            $this->entity1
        );

        $this->assertEquals(1, $this->entity1->getId());
        $this->assertEquals('x', $this->entity1->getField1());
        $this->assertEquals('y', $this->entity1->getField2());
        $this->assertEquals('z', $this->entity1->getField3());

    }

    /**
     * Test ability to transfer properties from one object to another
     */
    public function testTransfer()
    {
        $entity2 = new TestEntity1();
        $entity2->setField1('x');
        $entity2->setField2('y');
        $entity2->setField3('z');

        $this->assertEquals(null, $entity2->getId());
        $this->assertEquals('x', $entity2->getField1());
        $this->assertEquals('y', $entity2->getField2());
        $this->assertEquals('z', $entity2->getField3());

        $this->hydrator->transfer($this->entity1, $entity2);

        $this->assertEquals(null, $entity2->getId());
        $this->assertEquals('a', $entity2->getField1());
        $this->assertEquals('b', $entity2->getField2());
        $this->assertEquals('c', $entity2->getField3());
    }

    /**
     * Test ability to transfer data from one object to another... but only some properties
     */
    public function testTransferOnlySomeProperties()
    {
        $entity2 = new TestEntity1();
        $entity2->setField1('x');
        $entity2->setField2('y');
        $entity2->setField3('z');

        $this->assertEquals(null, $entity2->getId());
        $this->assertEquals('x', $entity2->getField1());
        $this->assertEquals('y', $entity2->getField2());
        $this->assertEquals('z', $entity2->getField3());

        $whitelistedProperties = array('field1');

        $this->hydrator->transfer($this->entity1, $entity2, $whitelistedProperties);

        $this->assertEquals(null, $entity2->getId());
        $this->assertEquals('a', $entity2->getField1());
        $this->assertEquals('y', $entity2->getField2());
        $this->assertEquals('z', $entity2->getField3());
    }
}