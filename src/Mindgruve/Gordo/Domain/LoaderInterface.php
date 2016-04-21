<?php

namespace Mindgruve\Gordo\Domain;

interface LoaderInterface
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
    public function buildDomainModel($domainModelClass);

}