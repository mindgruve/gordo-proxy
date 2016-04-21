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
     * @param $domainModelClass
     * @return object
     */
    public function build($domainModelClass);

}