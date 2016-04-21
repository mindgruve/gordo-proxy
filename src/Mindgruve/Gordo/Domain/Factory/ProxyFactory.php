<?php

namespace Mindgruve\Gordo\Domain\Factory;

use Mindgruve\Gordo\Domain\MetaDataReader;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class ProxyFactory
{
    /**
     * @var MetaDataReader
     */
    protected $metaDataReader;

    /**
     * @var array
     */
    protected $domainFactories = array();

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

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
    public function createProxy($obj)
    {
        $class = get_class($obj);
        $factory = new LazyLoadingValueHolderFactory();
        $domainFactories = &$this->domainFactories;
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($obj, $class, & $domainFactories) {
            $initializer = null;

            if (isset($domainFactories[$class])) {
                $domainFactory = $domainFactories[$class];
            } else {
                $domainFactory = new ModelFactory($class, $this->metaDataReader, $this);
                $domainFactories[$class] = $domainFactory;
            }

            $wrappedObject = $domainFactory->buildDomainModel($obj);

            return true;
        };

        return $factory->createProxy($this->metaDataReader->getDomainModelClass($class), $initializer);
    }
}