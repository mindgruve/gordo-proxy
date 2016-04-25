<?php

namespace Mindgruve\Gordo\Proxy;

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
    protected $transformers;

    /**
     * @var array
     */
    protected $factories;

    /**
     * @var array
     */
    protected $hydrators;


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
     * @param $object
     * @param Hydrator $hydrator
     * @param Transformer $transformer
     * @return mixed
     */
    public function transform($object, Hydrator $hydrator = null, Transformer $transformer = null)
    {
        $class = get_class($object);

        if (!$hydrator) {
            if (!isset($this->hydrators[$class])) {
                $this->hydrators[$class] = new Hydrator($class);
            }
            $hydrator =$this->hydrators[$class];
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