<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mindgruve\Gordo\Annotations\AnnotationReader;

class ProxyManager
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
     * @param EntityManagerInterface $em
     * @param AnnotationReader $annotationReader
     */
    public function __construct(
        EntityManagerInterface $em,
        AnnotationReader $annotationReader = null
    ) {
        $this->em = $em;
        if (!$annotationReader) {
            $annotationReader = new AnnotationReader($em);
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
     * @param Hydrator $hydrator
     * @param Transformer $transformer
     * @return array|mixed
     * @throws \Exception
     */
    public function transform(
        $item,
        Hydrator $hydrator = null,
        Transformer $transformer = null
    ) {

        if ($item instanceof ArrayCollection) {
            return $this->transformArrayCollection($item);
        } elseif (is_array($item)) {
            return $this->transformArray($item, $hydrator, $transformer);
        } elseif (is_object($item)) {
            return $this->transformObject($item, $hydrator, $transformer);
        }

        throw new \Exception('Unable to transform item');
    }

    public function transformArrayCollection(ArrayCollection $arrayCollection)
    {
        return new ArrayCollection($this->transform($arrayCollection->toArray()));
    }

    /**
     * @param array $array
     * @param Hydrator $hydrator
     * @param Transformer $transformer
     * @return array
     * @throws \Exception
     */
    public function transformArray(
        array $array,
        Hydrator $hydrator = null,
        Transformer $transformer = null
    ) {
        $return = array();
        foreach ($array as $item) {
            $return[] = $this->transform($item, $hydrator, $transformer);
        }

        return $return;
    }

    /**
     * @param $object
     * @param Hydrator $hydrator
     * @param Transformer $transformer
     * @return mixed
     */
    public function transformObject(
        $object,
        Hydrator $hydrator = null,
        Transformer $transformer = null
    ) {
        $class = get_class($object);

        if (!$hydrator) {
            if (!isset($this->hydrators[$class])) {
                $this->hydrators[$class] = new Hydrator($class);
            }
            $hydrator = $this->hydrators[$class];
        }

        if (!$transformer) {
            if (!isset($this->transformers[$class])) {
                $this->transformers[$class] = new Transformer(
                    $class,
                    $this->em,
                    $this->annotationReader,
                    $hydrator,
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