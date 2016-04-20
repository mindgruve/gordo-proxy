<?php

namespace Mindgruve\Gordo\Domain;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Mindgruve\Gordo\Domain\Hydrator as DomainHydrator;
use Mindgruve\Gordo\Domain\Annotations as DomainAnnotations;

class Factory
{
    /**
     * @var MetaDataReader
     */
    protected $reader;


    /**
     * Constructor
     */
    public function __construct(MetaDataReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Build the domain model based on the annotations in the class
     *
     * @param $obj
     * @return \ProxyManager\Proxy\VirtualProxyInterface
     */
    public function buildDomainModel($obj)
    {
        $class = get_class($obj);
        $hydrator = new DomainHydrator($class, $this->reader, $this);

        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($obj, $hydrator) {

            $initializer = null;
            $wrappedObject = $hydrator->buildDomainModel($obj);

            return true;
        };

        return $factory->createProxy($class, $initializer);
    }
}