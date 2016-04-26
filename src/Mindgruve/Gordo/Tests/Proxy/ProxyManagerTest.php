<?php

namespace Mindgruve\Gordo\Tests\Proxy;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Annotations\AnnotationReader;
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

    public function testTransformSynced1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity1);
        $proxy1->setField1('x');

        $this->assertEquals('a', $this->entity1->getField1());
        $this->assertEquals('b', $this->entity1->getField2());
        $this->assertEquals('c', $this->entity1->getField3());
        $this->assertEquals('x', $proxy1->getField1());
        $this->assertEquals('b', $proxy1->getField2());
        $this->assertEquals('c', $proxy1->getField3());
    }

    public function testTransformSynced2()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity2);
        $proxy1->setField1('x');
        $proxy1->setField2('y');
        $proxy1->setField3('z');

        $this->assertEquals('x', $this->entity2->getField1());
        $this->assertEquals('y', $this->entity2->getField2());
        $this->assertEquals('z', $this->entity2->getField3());
        $this->assertEquals('x', $proxy1->getField1());
        $this->assertEquals('y', $proxy1->getField2());
        $this->assertEquals('z', $proxy1->getField3());
    }

    public function testTransformNotSynced()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity1);
        $proxy1->setField1('x');

        $this->assertEquals('a', $this->entity1->getField1());
        $this->assertEquals('b', $this->entity1->getField2());
        $this->assertEquals('c', $this->entity1->getField3());

        $proxy1->syncEntity();

        $this->assertEquals('x', $this->entity1->getField1());
        $this->assertEquals('b', $this->entity1->getField2());
        $this->assertEquals('c', $this->entity1->getField3());
    }

    public function testTransformSelectedProperties()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity3);
        $proxy1->setField1('x');
        $proxy1->setField2('y');
        $proxy1->setField3('z');

        $this->assertEquals('x', $this->entity3->getField1());
        $this->assertEquals('b', $this->entity3->getField2());
        $this->assertEquals('c', $this->entity3->getField3());
        $this->assertEquals('x', $proxy1->getField1());
        $this->assertEquals('y', $proxy1->getField2());
        $this->assertEquals('z', $proxy1->getField3());
    }

    public function testTransformSelectedProperties4()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $classMetaDataMock = Mockery::mock('Doctrine\ORM\Mapping\ClassMetadata');
        $emMock->shouldReceive('getClassMetadata')->andReturn($classMetaDataMock);
        $classMetaDataMock->shouldReceive('getAssociationMappings')->andReturn(array());

        $sut = new ProxyManager($emMock);
        $proxy1 = $sut->transform($this->entity4);
        $proxy1->setField1('x');
        $proxy1->setField2('y');
        $proxy1->setField3('z');

        $this->assertEquals('a', $this->entity4->getField1());
        $this->assertEquals('b', $this->entity4->getField2());
        $this->assertEquals('c', $this->entity4->getField3());
        $this->assertEquals('x', $proxy1->getField1());
        $this->assertEquals('y', $proxy1->getField2());
        $this->assertEquals('z', $proxy1->getField3());

        $proxy1->syncEntity();

        $this->assertEquals('x', $this->entity4->getField1());
        $this->assertEquals('b', $this->entity4->getField2());
        $this->assertEquals('c', $this->entity4->getField3());
        $this->assertEquals('x', $proxy1->getField1());
        $this->assertEquals('y', $proxy1->getField2());
        $this->assertEquals('z', $proxy1->getField3());
    }
}