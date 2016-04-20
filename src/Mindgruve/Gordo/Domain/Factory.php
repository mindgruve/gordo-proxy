<?php

namespace Mindgruve\Gordo\Domain;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Mindgruve\Gordo\Domain\Hydrator as DomainHydrator;

class Factory
{
    /**
     * @var MetaDataReader
     */
    protected $metaDataReader;

    /**
     * @var array
     */
    protected $hydrators = array();

    /**
     * Constructor
     */
    public function __construct(MetaDataReader $metaDataReader)
    {
        $this->metaDataReader = $metaDataReader;
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
        $factory = new LazyLoadingValueHolderFactory();
        $hydrators = &$this->hydrators;
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($obj, $class, & $hydrators) {
            $initializer = null;

            if (isset($hydrators[$class])) {
                $hydrator = $hydrators[$class];
            } else {
                $hydrator = new DomainHydrator($class, $this->metaDataReader, $this);
                $hydrators[$class] = $hydrator;
            }

            $wrappedObject = $hydrator->buildDomainModel($obj);

            return true;
        };

        return $factory->createProxy($this->metaDataReader->getDomainModelClass($class), $initializer);
    }
}