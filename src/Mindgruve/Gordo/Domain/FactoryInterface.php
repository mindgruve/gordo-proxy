<?php

namespace Mindgruve\Gordo\Domain;

interface FactoryInterface
{

    /**
     * @param $domainModelClass
     * @return bool
     */
    public function supports($domainModelClass);

    /**
     * @param $proxyModelClass
     * @return object
     */
    public function build($proxyModelClass);

}