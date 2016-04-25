<?php

namespace Mindgruve\Gordo\Proxy;

interface FactoryInterface
{

    /**
     * @param $ProxyClass
     * @return bool
     */
    public function supports($ProxyClass);

    /**
     * @param $ProxyClass
     * @return object
     */
    public function build($ProxyClass);

}