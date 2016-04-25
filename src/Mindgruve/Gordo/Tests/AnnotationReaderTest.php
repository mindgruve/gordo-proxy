<?php

namespace Mindgruve\Gordo\Domain\Tests;

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

    public function testGetTransformAnnotations()
    {
        $emMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');

        $sut = new AnnotationReader($emMock);
        $annotations = $sut->getProxyAnnotations('Mindgruve\Gordo\Tests\TestEntity1');

        $this->assertTrue($annotations instanceof EntityProxy);
    }


}