<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GeneratedHydrator\Configuration;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use Doctrine\Common\Inflector\Inflector;

class EntityTransformer
{

    /**
     * @var
     */
    protected $class;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var
     */
    protected $hydrator;

    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var array
     */
    protected $entityDecorators = array();

    /**
     * @param $class
     * @param EntityManagerInterface $em
     * @param AnnotationReader $annotationReader
     */
    public function __construct(
        $class,
        EntityManagerInterface $em,
        AnnotationReader $annotationReader = null,
        Hydrator $hydrator = null
    ) {
        $this->em = $em;
        if (!$annotationReader) {
            $annotationReader = new AnnotationReader($em);
        }

        $this->class = $class;
        $this->annotationReader = $annotationReader;

        if(!$hydrator){
            $hydrator = new Hydrator($class, $annotationReader);
        }
        $this->hydrator = $hydrator;
    }

    /**
     * @param $objSrc
     * @return mixed
     */
    public function transform($objSrc)
    {
        $objSrcData = $this->hydrator->extract($objSrc);
        $entityProxyClass = $this->annotationReader->getModelProxyClass(get_class($objSrc));
        if ($entityProxyClass != $this->class) {

            $entityAnnotations = $this->annotationReader->getEntityAnnotations($this->class);
            $associations = $entityAnnotations->getAssociationMappings();

            foreach ($associations as $key => $association) {
                if (isset($objSrcData[$key])) {
                    $collection = $objSrcData[$key];
                    $items = array();
                    foreach ($collection as $item) {
                        $items[] = $item;
                    }
                    $objSrcData[$key] = new ArrayCollection($items);
                }
            }
            $objDest = $this->instantiate($entityProxyClass);
            $this->hydrator->hydrate($objSrcData, $objDest);

            /**
             * Check if EntityProxy
             */
            if ($this->isEntityProxy($objDest)) {

                $reflectionClass = new \ReflectionClass($objDest);

                $reflectionProperty = $reflectionClass->getProperty('entity');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($objDest, $objSrc);
                $reflectionProperty->setAccessible(false);

                $reflectionProperty = $reflectionClass->getProperty('hydrator');
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($objDest, $this->hydrator);
                $reflectionProperty->setAccessible(false);

                $proxiedMethods = array();
                foreach(array_keys($objSrcData) as $property){
                    $proxiedMethods[] = Inflector::camelize('set_'.$property);
                }
                foreach($associations as $associationKey => $association){
                    $associationKey = Inflector::singularize($associationKey);
                    $proxiedMethods[] = Inflector::camelize('add_'.$associationKey);
                    $proxiedMethods[] = Inflector::camelize('remove_'.$associationKey);
                }

                $factory = new Factory();
                $proxy = $factory->createProxy($objDest, array());
                foreach($proxiedMethods as $proxiedMethod){
                    $proxy->setMethodSuffixInterceptor(
                        $proxiedMethod,
                        function ($proxy, $instance, $method, $params) {
                            $instance->syncEntity();
                        }
                    );
                }

                return $proxy;

            }

            return $objDest;
        }

        return $objSrc;
    }

    /**
     * @param FactoryInterface $factory
     * @return $this
     */
    public function registerFactory(FactoryInterface $factory)
    {
        $this->factories[] = $factory;

        return $this;
    }

    protected function isEntityProxy($obj)
    {
        if (array_key_exists('Mindgruve\Gordo\Domain\EntityProxyTrait', class_uses($obj))) {
            return true;
        }

        return false;
    }

    /**
     * @param $entityProxyClass
     * @return object
     */
    protected function instantiate($entityProxyClass)
    {
        foreach ($this->factories as $factory) {

            /**
             * @var FactoryInterface $factory
             */
            if ($factory->supports($entityProxyClass)) {
                return $factory->build($entityProxyClass);
            }
        }

        return new $entityProxyClass();

    }
}