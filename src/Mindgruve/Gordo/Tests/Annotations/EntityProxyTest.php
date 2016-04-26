<?php

namespace Mindgruve\Gordo\Tests\Annotations;
use Mindgruve\Gordo\Annotations\Proxy as ProxyAnnotation;

class ProxyTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPublicProperties()
    {
        $sut = new ProxyAnnotation();
        $this->assertClassHasAttribute('target', get_class($sut));
        $this->assertClassHasAttribute('syncProperties', get_class($sut));
        $this->assertClassHasAttribute('syncMethods', get_class($sut));
    }

}