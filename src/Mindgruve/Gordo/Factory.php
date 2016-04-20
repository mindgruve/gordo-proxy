<?php

namespace Mindgruve\Gordo;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Doctrine\Common\Annotations\AnnotationReader;

class Factory
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * @param $class
     * @return \Mindgruve\Gordo\Annotations | null
     */
    public function getAnnotations($class)
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof \Mindgruve\Gordo\Annotations) {
                return $annotation;
            }
        }

        return null;
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
        $hydrator = new \Mindgruve\Gordo\Hydrator($class);

        $domainModelClass = $class;
        $annotations = $this->getAnnotations($class);
        if($annotations){
            $domainModelClass = $annotations->domainModel;
        }

        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($hydrator, $obj, $domainModelClass) {
            $initializer = null;
            $wrappedObject = $obj;

            if ($domainModelClass) {
                $wrappedObject = $hydrator->transfer($obj, new $domainModelClass());
            }

            return true; // confirm that initialization occurred correctly
        };

        return $factory->createProxy($domainModelClass, $initializer);
    }
}