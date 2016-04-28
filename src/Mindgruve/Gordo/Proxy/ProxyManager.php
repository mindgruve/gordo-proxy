<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Mindgruve\Gordo\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ObjectManager;

class ProxyManager
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    protected $doctrineProxyResolver = array();

    /**
     * @var array
     */
    protected $transformers = array();

    /**
     * @var array
     */
    protected $factories = array();

    /**
     * @var array
     */
    protected $hydrators = array();


    /**
     * @param ObjectManager $objectManager
     * @param AnnotationReader $annotationReader
     * @param array $doctrineProxyNamespaces
     */
    public function __construct(
        ObjectManager $objectManager,
        AnnotationReader $annotationReader = null,
        array $doctrineProxyNamespaces = array(
            'ORM'      => 'Proxies',
            'PHPCR'    => 'PHPCRProxies',
            'MongoODM' => 'MongoDBODMProxies',
        )
    ) {
        $this->objectManager = $objectManager;
        if (!$annotationReader) {
            $annotationReader = new AnnotationReader($objectManager, new DoctrineProxyResolver($doctrineProxyNamespaces));
        }

        $this->annotationReader = $annotationReader;
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * @param $item
     * @param Transformer $transformer
     * @return array|mixed
     * @throws \Exception
     */
    public function transform(
        $item,
        Transformer $transformer = null
    ) {

        if ($item instanceof ArrayCollection) {
            return $this->transformArrayCollection($item);
        } elseif (is_array($item)) {
            return $this->transformArray($item, $transformer);
        } elseif (is_object($item)) {
            return $this->transformObject($item, $transformer);
        }

        throw new \Exception('Unable to transform item');
    }

    public function transformArrayCollection(ArrayCollection $arrayCollection)
    {
        return new ArrayCollection($this->transform($arrayCollection->toArray()));
    }

    /**
     * @param array $array
     * @param Transformer $transformer
     * @return array
     * @throws \Exception
     */
    public function transformArray(
        array $array,
        Transformer $transformer = null
    ) {
        $return = array();
        foreach ($array as $item) {
            $return[] = $this->transform($item, $transformer);
        }

        return $return;
    }

    /**
     * @param $object
     * @param Transformer $transformer
     * @return mixed
     */
    public function transformObject(
        $object,
        Transformer $transformer = null
    ) {
        $class = get_class($object);


        if (!$transformer) {
            if (!isset($this->transformers[$class])) {
                $this->transformers[$class] = new Transformer(
                    $class,
                    $this->annotationReader,
                    $this
                );
            }
            $transformer = $this->transformers[$class];
        }

        /**
         * @var Transformer $transformer
         */

        return $transformer->transform($object);
    }

    /**
     * Register a factory for the target Object
     *
     * @param FactoryInterface $factory
     * @return $this
     */
    public function registerFactory(FactoryInterface $factory)
    {
        $this->factories[] = $factory;

        return $this;
    }

    /**
     * Create a new target object, optionally using a factory method
     *
     * @param $ProxyClass
     * @return object
     */
    public function instantiate($ProxyClass)
    {
        foreach ($this->factories as $factory) {

            /**
             * @var FactoryInterface $factory
             */
            if ($factory->supports($ProxyClass)) {
                return $factory->build($ProxyClass);
            }
        }

        return new $ProxyClass();

    }

}