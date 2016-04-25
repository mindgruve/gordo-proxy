<?php

namespace Mindgruve\Gordo\Tests\Annotations;

use Mindgruve\Gordo\Annotations\AnnotationReader;
use Mindgruve\Gordo\Annotations\EntityProxy;
use Mockery;

class AnnotationReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleConstructor()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);

    }

    public function testFullConstructor()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $doctrineReaderMock = Mockery::mock('Doctrine\Common\Annotations\Reader');
        $nameSpaces = array('Doctrine\ORM\Mapping', 'Mindgruve\Gordo\Proxy', 'Test\Namespace');
        $cacheProvider = Mockery::mock('Doctrine\Common\Cache\CacheProvider');

        $doctrineReaderMock->shouldReceive('addNamespace')->with('Doctrine\ORM\Mapping');
        $doctrineReaderMock->shouldReceive('addNamespace')->with('Mindgruve\Gordo\Proxy');
        $doctrineReaderMock->shouldReceive('addNamespace')->with('Test\Namespace');

        $sut = new AnnotationReader($emMock, $doctrineReaderMock, $nameSpaces, $cacheProvider);
    }

    public function testGetProxyAnnotations()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $proxyAnnoation = $sut->getProxyAnnotations('Mindgruve\Gordo\Tests\Entity\TestEntity1');

        $this->assertTrue($proxyAnnoation instanceof EntityProxy);
        $this->assertEquals('Mindgruve\Gordo\Tests\Proxy\TestProxy1', $proxyAnnoation->target);
        $this->assertEquals(false, $proxyAnnoation->syncAuto);
        $this->assertEquals(array(), $proxyAnnoation->syncListeners);
        $this->assertEquals(array(), $proxyAnnoation->syncProperties);
    }

    public function testGetTargetClass()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxyTargetClass('Mindgruve\Gordo\Tests\Entity\TestEntity1');
        $this->assertEquals('Mindgruve\Gordo\Tests\Proxy\TestProxy1', $properties);
    }

    public function testGetProxySyncProperties1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncedProperties('Mindgruve\Gordo\Tests\Entity\TestEntity1');
        $this->assertEquals(array(), $properties);
    }

    public function testGetProxySyncProperties2()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncedProperties('Mindgruve\Gordo\Tests\Entity\TestEntity2');
        $this->assertEquals(array('*'), $properties);
    }

    public function testGetProxySyncProperties3()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncedProperties('Mindgruve\Gordo\Tests\Entity\TestEntity3');
        $this->assertEquals(array('field1'), $properties);
    }

    public function testGetProxySyncAuto1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncAuto('Mindgruve\Gordo\Tests\Entity\TestEntity1');
        $this->assertEquals(false, $properties);
    }

    public function testGetProxySyncAuto2()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncAuto('Mindgruve\Gordo\Tests\Entity\TestEntity2');
        $this->assertEquals(true, $properties);
    }

    public function testGetProxySyncAuto3()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncAuto('Mindgruve\Gordo\Tests\Entity\TestEntity3');
        $this->assertEquals(false, $properties);
    }

    public function testGetProxySyncListeners1()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncListeners('Mindgruve\Gordo\Tests\Entity\TestEntity1');
        $this->assertEquals(array(), $properties);
    }

    public function testGetProxySyncListeners2()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncListeners('Mindgruve\Gordo\Tests\Entity\TestEntity2');
        $this->assertEquals(array('*'), $properties);
    }

    public function testGetProxySyncListeners3()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $properties = $sut->getProxySyncListeners('Mindgruve\Gordo\Tests\Entity\TestEntity3');
        $this->assertEquals(array('setField1'), $properties);
    }
}