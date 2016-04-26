<?php

namespace Mindgruve\Gordo\Tests\Proxy;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Annotations\AnnotationReader;
use Mindgruve\Gordo\Proxy\ProxyConstants;
use Mindgruve\Gordo\Proxy\ProxyManager;
use Mindgruve\Gordo\Tests\Entity\TestEntity1;
use Mindgruve\Gordo\Tests\Entity\TestEntity2;
use Mindgruve\Gordo\Tests\Entity\TestEntity3;
use Mindgruve\Gordo\Tests\Entity\TestEntity4;
use Mindgruve\Gordo\Tests\Entity\TestProxy1;
use Mockery;

class ProxyManagerTest extends \PHPUnit_Framework_TestCase
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
     * @var TestEntity2
     */
    protected $entity2;

    /**
     * @var TestEntity3
     */
    protected $entity3;

    /**
     * @var TestEntity4
     */
    protected $entity4;

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

        $entity2 = new TestEntity2();
        $entity2->setField1('a');
        $entity2->setField2('b');
        $entity2->setField3('c');
        $this->entity2 = $entity2;

        $entity3 = new TestEntity3();
        $entity3->setField1('a');
        $entity3->setField2('b');
        $entity3->setField3('c');
        $this->entity3 = $entity3;

        $entity4 = new TestEntity4();
        $entity4->setField1('a');
        $entity4->setField2('b');
        $entity4->setField3('c');
        $this->entity4 = $entity4;

        $hydrator = new Hydrator(get_class($entity1));
        $this->hydrator = $hydrator;
    }

    public function testConstructor()
    {
        // Test without passing in annotation reader
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $sut = new ProxyManager($emMock);

        $this->assertTrue($sut->getAnnotionReader() instanceof AnnotationReader);

        // Test by passing in annotation reader
        $em = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $arMock = Mockery::mock('Mindgruve\Gordo\Annotations\AnnotationReader');

        $sut = new ProxyManager($em, $arMock);
        $this->assertEquals($arMock, $sut->getAnnotionReader());
    }

    public function testTransform()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity1);

        $this->assertTrue($proxy1 instanceof TestProxy1);
        $this->assertEquals('a', $proxy1->getField1());
        $this->assertEquals('b', $proxy1->getField2());
        $this->assertEquals('c', $proxy1->getField3());
    }

    public function testTransformSyncedEntity1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);

        $this->setup();
        $proxy2 = $sut->transform($this->entity1);

        // Modify Proxy
        $proxy2->setField1('x');
        $proxy2->setField2('y');
        $proxy2->setField3('z');

        // Entity should be unchanged
        $this->assertEquals('a', $this->entity1->getField1());
        $this->assertEquals('b', $this->entity1->getField2());
        $this->assertEquals('c', $this->entity1->getField3());

        // Manually sync to entity
        $proxy2->syncEntity();

        // Confirm properties updated
        $this->assertEquals('x', $this->entity1->getField1());
        $this->assertEquals('y', $this->entity1->getField2());
        $this->assertEquals('z', $this->entity1->getField3());

        // Now test the reverse... update entity
        $this->entity1->setField1('1');
        $this->entity1->setField2('2');
        $this->entity1->setField3('3');

        // Proxy unchanged
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // SYNC_PROPERTIES_NONE should have no impact
        $proxy2->syncEntity(ProxyConstants::SYNC_FROM_ENTITY, ProxyConstants::SYNC_PROPERTIES_NONE);

        // Confirm properties changed
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // Pull in changes from Entity
        $proxy2->syncEntity(ProxyConstants::SYNC_FROM_ENTITY);

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
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy2 = $sut->transform($this->entity2);

        // Modify Proxy
        $proxy2->setField1('x');
        $proxy2->setField2('y');
        $proxy2->setField3('z');

        // Confirm changes on underlying entity
        $this->assertEquals('x', $this->entity2->getField1());
        $this->assertEquals('y', $this->entity2->getField2());
        $this->assertEquals('z', $this->entity2->getField3());

        // Now test the reverse... update entity
        $this->entity2->setField1('1');
        $this->entity2->setField2('2');
        $this->entity2->setField3('3');

        // Confirm values on proxy unchanged
        $this->assertEquals('x', $proxy2->getField1());
        $this->assertEquals('y', $proxy2->getField2());
        $this->assertEquals('z', $proxy2->getField3());

        // Pull changes up from underlying entity
        $proxy2->syncEntity(ProxyConstants::SYNC_FROM_ENTITY);

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
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy3 = $sut->transform($this->entity3);

        // Modify Proxy
        $proxy3->setField1('x');
        $proxy3->setField2('y');
        $proxy3->setField3('z');

        // Confirm changes on underlying entity
        $this->assertEquals('x', $this->entity3->getField1());
        $this->assertEquals('b', $this->entity3->getField2());
        $this->assertEquals('c', $this->entity3->getField3());

        // Now test the reverse... update entity
        $this->entity3->setField1('1');
        $this->entity3->setField2('2');
        $this->entity3->setField3('3');

        // Confirm proxy not changed
        $this->assertEquals('x', $proxy3->getField1());
        $this->assertEquals('y', $proxy3->getField2());
        $this->assertEquals('z', $proxy3->getField3());

        // Pull up changes from entity
        $proxy3->syncEntity(ProxyConstants::SYNC_FROM_ENTITY);

        // Confirm Changes
        $this->assertEquals('1', $proxy3->getField1());
        $this->assertEquals('y', $proxy3->getField2());
        $this->assertEquals('z', $proxy3->getField3());

        // Pull All Properties
        $proxy3->syncEntity(ProxyConstants::SYNC_FROM_ENTITY, ProxyConstants::SYNC_PROPERTIES_ALL);

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
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy4 = $sut->transform($this->entity4);

        // Modify Proxy
        $proxy4->setField1('x');
        $proxy4->setField2('y');
        $proxy4->setField3('z');

        // Confirm entity unchanged
        $this->assertEquals('a', $this->entity4->getField1());
        $this->assertEquals('b', $this->entity4->getField2());
        $this->assertEquals('c', $this->entity4->getField3());

        // Sync down changes
        $proxy4->syncEntity();

        // Confirm entity updated
        $this->assertEquals('x', $this->entity4->getField1());
        $this->assertEquals('b', $this->entity4->getField2());
        $this->assertEquals('c', $this->entity4->getField3());

        // Now test the reverse... update entity
        $this->entity4->setField1('1');
        $this->entity4->setField2('2');
        $this->entity4->setField3('3');

        // confirm no changes
        $this->assertEquals('x', $proxy4->getField1());
        $this->assertEquals('y', $proxy4->getField2());
        $this->assertEquals('z', $proxy4->getField3());

        // pull data from entity
        $proxy4->syncEntity(ProxyConstants::SYNC_FROM_ENTITY);

        // confirm changes
        $this->assertEquals('1', $proxy4->getField1());
        $this->assertEquals('y', $proxy4->getField2());
        $this->assertEquals('z', $proxy4->getField3());

        // pull all properties
        $proxy4->syncEntity(ProxyConstants::SYNC_FROM_ENTITY, ProxyConstants::SYNC_PROPERTIES_ALL);

        // confirm changes
        $this->assertEquals('1', $proxy4->getField1());
        $this->assertEquals('2', $proxy4->getField2());
        $this->assertEquals('3', $proxy4->getField3());
    }
}