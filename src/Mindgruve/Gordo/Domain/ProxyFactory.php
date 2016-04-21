<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\ORM\EntityManagerInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class ProxyFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

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
    public function __construct(EntityManagerInterface $em, AnnotationReader $annotationReader = null)
    {
        $this->em = $em;

        if(!$annotationReader){
            $annotationReader = new AnnotationReader($em);
        }

        $this->annotationReader = $annotationReader;
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
                $domainFactory = new EntityDecorator($class, $this->em, $this, $this->annotationReader);
                $domainFactories[$class] = $domainFactory;
            }

            $wrappedObject = $domainFactory->decorate($obj);

            return true;
        };

        return $factory->createProxy($this->annotationReader->getProxyModelClass($class), $initializer);
    }
}