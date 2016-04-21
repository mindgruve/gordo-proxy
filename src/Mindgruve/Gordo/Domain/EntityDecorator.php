<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GeneratedHydrator\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

class EntityDecorator
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
        AnnotationReader $annotationReader = null
    ) {
        $this->em = $em;
        if (!$annotationReader) {
            $annotationReader = new AnnotationReader($em);
        }

        $this->class = $class;
        $this->annotationReader = $annotationReader;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
    }

    /**
     * @param $objSrc
     * @return mixed
     */
    public function decorate($objSrc)
    {
        $data = $this->hydrator->extract($objSrc);
        $entityProxyClass = $this->annotationReader->getModelProxyClass(get_class($objSrc));
        if ($entityProxyClass != $this->class) {

            $entityAnnotations = $this->annotationReader->getEntityAnnotations($this->class);
            $associations = $entityAnnotations->getAssociationMappings();

            foreach ($associations as $key => $association) {
                if (isset($data[$key])) {
                    $collection = $data[$key];
                    $items = array();
                    foreach ($collection as $item) {
                        $items[] = $item;
                    }
                    $data[$key] = new ArrayCollection($items);
                }
            }
            $objDest = $this->instantiate($entityProxyClass);

            /**
             * Check if EntityProxy
             */
            if(array_key_exists('Mindgruve\Gordo\Domain\EntityProxyTrait',class_uses($objDest))){
                $objDest->setEntity($objSrc);
            }

            return $this->hydrator->hydrate($data, $objDest);
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

    /**
     * @param $obj
     * @return \ProxyManager\Proxy\VirtualProxyInterface
     */
    protected function createLazyLoadingProxy($obj)
    {
        $class = get_class($obj);
        $factory = new LazyLoadingValueHolderFactory();
        $entityDecorators = &$this->entityDecorators;
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($obj, $class, & $entityDecorators) {
            $initializer = null;

            if (isset($entityDecorators[$class])) {
                $entityDecorator = $entityDecorators[$class];
            } else {
                $entityDecorator = new EntityDecorator($class, $this->em, $this->annotationReader);
                $entityDecorators[$class] = $entityDecorator;
            }

            $wrappedObject = $entityDecorator->decorate($obj);

            return true;
        };

        return $factory->createProxy($this->annotationReader->getModelProxyClass($class), $initializer);
    }

}