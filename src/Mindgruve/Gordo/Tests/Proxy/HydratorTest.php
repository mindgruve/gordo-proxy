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
    protected $dataObject1;

    /**
     * Data Fixture SETUP
     */
    public function setup()
    {
        $dataObject1 = new TestEntity1();
        $dataObject1->setField1('a');
        $dataObject1->setField2('b');
        $dataObject1->setField3('c');
        $this->dataObject1 = $dataObject1;

        $hydrator = new Hydrator(get_class($dataObject1));
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
            $this->hydrator->extract($this->dataObject1)
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
            $this->dataObject1
        );

        $this->assertEquals(1, $this->dataObject1->getId());
        $this->assertEquals('x', $this->dataObject1->getField1());
        $this->assertEquals('y', $this->dataObject1->getField2());
        $this->assertEquals('z', $this->dataObject1->getField3());

    }

    /**
     * Test ability to transfer properties from one object to another
     */
    public function testTransfer()
    {
        $dataObject2 = new TestEntity1();
        $dataObject2->setField1('x');
        $dataObject2->setField2('y');
        $dataObject2->setField3('z');

        $this->assertEquals(null, $dataObject2->getId());
        $this->assertEquals('x', $dataObject2->getField1());
        $this->assertEquals('y', $dataObject2->getField2());
        $this->assertEquals('z', $dataObject2->getField3());

        $this->hydrator->transfer($this->dataObject1, $dataObject2);

        $this->assertEquals(null, $dataObject2->getId());
        $this->assertEquals('a', $dataObject2->getField1());
        $this->assertEquals('b', $dataObject2->getField2());
        $this->assertEquals('c', $dataObject2->getField3());
    }

    /**
     * Test ability to transfer data from one object to another... but only some properties
     */
    public function testTransferOnlySomeProperties()
    {
        $dataObject2 = new TestEntity1();
        $dataObject2->setField1('x');
        $dataObject2->setField2('y');
        $dataObject2->setField3('z');

        $this->assertEquals(null, $dataObject2->getId());
        $this->assertEquals('x', $dataObject2->getField1());
        $this->assertEquals('y', $dataObject2->getField2());
        $this->assertEquals('z', $dataObject2->getField3());

        $whitelistedProperties = array('field1');

        $this->hydrator->transfer($this->dataObject1, $dataObject2, $whitelistedProperties);

        $this->assertEquals(null, $dataObject2->getId());
        $this->assertEquals('a', $dataObject2->getField1());
        $this->assertEquals('y', $dataObject2->getField2());
        $this->assertEquals('z', $dataObject2->getField3());
    }
}