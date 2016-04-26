<?php

namespace Mindgruve\Gordo\Tests\Annotations;
use Mindgruve\Gordo\Annotations\EntityProxy as EntityProxyAnnotation;

class EntityProxyTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPublicProperties()
    {
        $sut = new EntityProxyAnnotation();
        $this->assertClassHasAttribute('target', get_class($sut));
        $this->assertClassHasAttribute('syncAuto', get_class($sut));
        $this->assertClassHasAttribute('syncProperties', get_class($sut));
        $this->assertClassHasAttribute('syncMethods', get_class($sut));
    }

}