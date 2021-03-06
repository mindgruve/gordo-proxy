<?php

namespace Mindgruve\Gordo\Tests\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Mindgruve\Gordo\Annotations\AnnotationReader;
use Mindgruve\Gordo\Proxy\Constants;
use Mindgruve\Gordo\Proxy\ProxyManager;
use Mindgruve\Gordo\Tests\Entity\TestAssociation1;
use Mindgruve\Gordo\Tests\Entity\TestAssociationProxy1;
use Mindgruve\Gordo\Tests\Entity\TestEntity1;
use Mindgruve\Gordo\Tests\Entity\TestEntity2;
use Mindgruve\Gordo\Tests\Entity\TestEntity3;
use Mindgruve\Gordo\Tests\Entity\TestEntity4;
use Mindgruve\Gordo\Tests\Entity\TestEntity5;
use Mindgruve\Gordo\Tests\Entity\TestProxy1;
use Mindgruve\Gordo\Tests\Entity\TestProxy2;
use Mindgruve\Gordo\Tests\Entity\TestProxy3;
use Mindgruve\Gordo\Tests\Entity\TestProxy4;
use Mockery;

class ProxyManagerTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @var TestEntity1
     */
    protected $dataObject1;

    /**
     * @var TestEntity2
     */
    protected $dataObject2;

    /**
     * @var TestEntity3
     */
    protected $dataObject3;

    /**
     * @var TestEntity4
     */
    protected $dataObject4;

    /**
     * @var TestEntity5
     */
    protected $dataObject5;

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

        $dataObject2 = new TestEntity2();
        $dataObject2->setField1('a');
        $dataObject2->setField2('b');
        $dataObject2->setField3('c');
        $this->dataObject2 = $dataObject2;

        $dataObject3 = new TestEntity3();
        $dataObject3->setField1('a');
        $dataObject3->setField2('b');
        $dataObject3->setField3('c');
        $this->dataObject3 = $dataObject3;

        $dataObject4 = new TestEntity4();
        $dataObject4->setField1('a');
        $dataObject4->setField2('b');
        $dataObject4->setField3('c');
        $this->dataObject4 = $dataObject4;

        $dataObject5 = new TestEntity5();
        $dataObject5->setField1('a');
        $dataObject5->setField2('b');
        $dataObject5->setField3('c');
        $this->dataObject5 = $dataObject5;

    }

    public function testConstructor()
    {
        // Test without passing in annotation reader
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $sut = new ProxyManager($emMock);

        $this->assertTrue($sut->getAnnotationReader() instanceof AnnotationReader);

        // Test by passing in annotation reader
        $em = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $arMock = Mockery::mock('Mindgruve\Gordo\Annotations\AnnotationReader');

        $sut = new ProxyManager($em, $arMock);
        $this->assertEquals($arMock, $sut->getAnnotationReader());
    }

    public function testTransform()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->dataObject1);

        $this->assertTrue($proxy1 instanceof TestProxy1);
        $this->assertEquals('a', $proxy1->getField1());
        $this->assertEquals('b', $proxy1->getField2());
        $this->assertEquals('c', $proxy1->getField3());
    }

    /**
     * @Proxy notation not used so transform() should return original object
     * @throws \Exception
     */
    public function testNullTransform()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy = $sut->transform($this->dataObject5);
        $this->assertEquals($this->dataObject5, $proxy);
    }

    public function testTransformSyncedEntity1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);

        $this->setup();
        $proxy2 = $sut->transform($this->dataObject1);

        // Modify Proxy
        $proxy2->setField1('x');
        $proxy2->setField2('y');
        $proxy2->setField3('z');

        // data object should be unchanged
        $this->assertEquals('a', $this->dataObject1->getField1());
        $this->assertEquals('b', $this->dataObject1->getField2());
        $this->assertEquals('c', $this->dataObject1->getField3());

        // Manually sync to data object
        $proxy2->syncData();

        // Confirm properties updated
        $this->assertEquals('x', $this->dataObject1->getField1());
        $this->assertEquals('y', $this->dataObject1->getField2());
        $this->assertEquals('z', $this->dataObject1->getField3());

        // Now test the reverse... update data object
        $this->dataObject1->setField1('1');
        $this->dataObject1->setField2('2');
        $this->dataObject1->setField3('3');

        // Proxy unchanged
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // SYNC_PROPERTIES_NONE should have no impact
        $proxy2->syncData(Constants::UPDATE_PROXY, Constants::SYNC_PROPERTIES_NONE);

        // Confirm properties changed
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // Pull in changes from data object
        $proxy2->syncData(Constants::UPDATE_PROXY);

        // Confirm changes on proxy
        $this->assertEquals('1', $proxy2->getField1());
        $this->assertEquals('2', $proxy2->getField2());
        $this->assertEquals('3', $proxy2->getField3());
    }

    public function testTransformSyncedEntity2()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy2 = $sut->transform($this->dataObject2);

        // Modify Proxy
        $proxy2->setField1('x');
        $proxy2->setField2('y');
        $proxy2->setField3('z');

        // Confirm changes on underlying data object
        $this->assertEquals('x', $this->dataObject2->getField1());
        $this->assertEquals('y', $this->dataObject2->getField2());
        $this->assertEquals('z', $this->dataObject2->getField3());

        // Now test the reverse... update data object
        $this->dataObject2->setField1('1');
        $this->dataObject2->setField2('2');
        $this->dataObject2->setField3('3');

        // Confirm values on proxy unchanged
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // Pull changes up from underlying data object
        $proxy2->syncData(Constants::UPDATE_PROXY);

        // Confirm changes
        $this->assertEquals('1', $proxy2->getField1());
        $this->assertEquals('2', $proxy2->getField2());
        $this->assertEquals('3', $proxy2->getField3());
    }

    public function testTransformSyncEntity3()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy3 = $sut->transform($this->dataObject3);

        // Modify Proxy
        $proxy3->setField1('x');
        $proxy3->setField2('y');
        $proxy3->setField3('z');

        // Confirm changes on underlying data object
        $this->assertEquals('x', $this->dataObject3->getField1());
        $this->assertEquals('b', $this->dataObject3->getField2());
        $this->assertEquals('c', $this->dataObject3->getField3());

        // Now test the reverse... update data object
        $this->dataObject3->setField1('1');
        $this->dataObject3->setField2('2');
        $this->dataObject3->setField3('3');

        // Confirm proxy not changed
        $this->assertEquals('x', $proxy3->getField1());
        $this->assertEquals('y', $proxy3->getField2());
        $this->assertEquals('z', $proxy3->getField3());

        // Pull up changes from data object
        $proxy3->syncData(Constants::UPDATE_PROXY);

        // Confirm Changes
        $this->assertEquals('1', $proxy3->getField1());
        $this->assertEquals('y', $proxy3->getField2());
        $this->assertEquals('z', $proxy3->getField3());

        // Pull All Properties
        $proxy3->syncData(Constants::UPDATE_PROXY, Constants::SYNC_PROPERTIES_ALL);

        // Confirm Changes
        $this->assertEquals('1', $proxy3->getField1());
        $this->assertEquals('2', $proxy3->getField2());
        $this->assertEquals('3', $proxy3->getField3());
    }

    public function testTransformSyncEntity4()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy4 = $sut->transform($this->dataObject4);

        // Modify Proxy
        $proxy4->setField2('y');
        $proxy4->setField3('z');

        // Confirm data object unchanged
        $this->assertEquals('a', $this->dataObject4->getField1());
        $this->assertEquals('b', $this->dataObject4->getField2());
        $this->assertEquals('c', $this->dataObject4->getField3());

        // Sync down changes
        $proxy4->syncData();

        // Confirm data object updated
        $this->assertEquals('a', $this->dataObject4->getField1());
        $this->assertEquals('b', $this->dataObject4->getField2());
        $this->assertEquals('c', $this->dataObject4->getField3());

        // Now test the reverse... update data object
        $this->dataObject4->setField1('1');
        $this->dataObject4->setField2('2');
        $this->dataObject4->setField3('3');

        // confirm no changes
        $this->assertEquals('a', $proxy4->getField1());
        $this->assertEquals('y', $proxy4->getField2());
        $this->assertEquals('z', $proxy4->getField3());

        // pull data from data object
        $proxy4->syncData(Constants::UPDATE_PROXY);

        // confirm changes
        $this->assertEquals('1', $proxy4->getField1());
        $this->assertEquals('y', $proxy4->getField2());
        $this->assertEquals('z', $proxy4->getField3());

        // pull all properties
        $proxy4->syncData(Constants::UPDATE_PROXY, Constants::SYNC_PROPERTIES_ALL);

        // confirm changes
        $this->assertEquals('1', $proxy4->getField1());
        $this->assertEquals('2', $proxy4->getField2());
        $this->assertEquals('3', $proxy4->getField3());
    }

    public function testTransformingArray()
    {
        $array = array($this->dataObject1, $this->dataObject2, $this->dataObject3, $this->dataObject4);

        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxyArray = $sut->transform($array);

        $this->assertEquals(4, count($proxyArray));
        $this->assertTrue(is_array($proxyArray));
        $this->assertTrue($proxyArray[0] instanceof TestProxy1);
        $this->assertTrue($proxyArray[1] instanceof TestProxy2);
        $this->assertTrue($proxyArray[2] instanceof TestProxy3);
        $this->assertTrue($proxyArray[3] instanceof TestProxy4);
    }

    public function testTransformingArrayCollection()
    {
        $array = array($this->dataObject1, $this->dataObject2, $this->dataObject3, $this->dataObject4);
        $arrayCollection = new ArrayCollection($array);

        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxyArray = $sut->transform($arrayCollection);

        $this->assertEquals(4, count($proxyArray));
        $this->assertTrue($proxyArray instanceof ArrayCollection);
        $this->assertTrue($proxyArray[0] instanceof TestProxy1);
        $this->assertTrue($proxyArray[1] instanceof TestProxy2);
        $this->assertTrue($proxyArray[2] instanceof TestProxy3);
        $this->assertTrue($proxyArray[3] instanceof TestProxy4);
    }


    public function testOneToOneAssociation()
    {
        $association1 = new TestAssociation1();
        $association1->setField1('a');
        $this->dataObject1->setAssociation1($association1);

        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(array('association1'));

        $sut = new ProxyManager($emMock);
        $proxy = $sut->transform($this->dataObject1);

        $this->assertTrue($proxy->getAssociation1() instanceof TestAssociationProxy1);
        $this->assertEquals('a', $proxy->getAssociation1()->getField1());

    }

    public function testManyToOneAssociation()
    {
        $association1 = new TestAssociation1();
        $association1->setField1('a');

        $association2 = new TestAssociation1();
        $association2->setField1('b');

        $collection = new ArrayCollection(array($association1, $association2));
        $this->dataObject1->setAssociation2($collection);

        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationNames')->andReturn(
            array('association1', 'association2')
        );

        $sut = new ProxyManager($emMock);
        $proxy = $sut->transform($this->dataObject1);

        $this->assertTrue($proxy->getAssociation2() instanceof ArrayCollection);
        $this->assertEquals(2, count($proxy->getAssociation2()));

        $item1 = $proxy->getAssociation2()[0];
        $item2 = $proxy->getAssociation2()[1];

        $this->assertTrue($item1 instanceof TestAssociationProxy1);
        $this->assertEquals('a', $item1->getField1());
        $this->assertTrue($item2 instanceof TestAssociationProxy1);
        $this->assertEquals('b', $item2->getField1());
    }
}