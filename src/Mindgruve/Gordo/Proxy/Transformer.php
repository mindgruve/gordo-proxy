<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use Doctrine\Common\Inflector\Inflector;
use Mindgruve\Gordo\Annotations\AnnotationReader;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

use GeneratedHydrator\Configuration;

class Transformer
{

    /**
     * @var
     */
    protected $class;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @var ProxyManager
     */
    protected $proxyManager;


    /**
     * Constructor
     *
     * @param $class
     * @param AnnotationReader $annotationReader
     * @param ProxyManager $proxyManager
     * @internal param EntityManagerInterface $em
     */
    public function __construct(
        $class,
        AnnotationReader $annotationReader,
        ProxyManager $proxyManager
    ) {
        $this->class = $class;
        $this->annotationReader = $annotationReader;
        $this->proxyManager = $proxyManager;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
    }

    /**
     * Transform Entity to proxy object
     *
     * @param $objSrc
     * @return mixed
     * @throws \Exception
     */
    public function transform($objSrc)
    {
        $objSrcData = $this->hydrator->extract($objSrc);
        $proxyClass = $this->annotationReader->getProxyTargetClass(get_class($objSrc));

        if(!$proxyClass){
            return $objSrc;
        }

        if ($proxyClass != $this->class) {

            $doctrineAnnotations = $this->annotationReader->getDoctrineAnnotations($this->class);
            $associations = $doctrineAnnotations->getAssociationNames();

            /**
             * Lazy Load Associations
             */
            foreach ($associations as $key) {
                if (isset($objSrcData[$key])) {
                    $propertyValue = $objSrcData[$key];
                    $factory = new LazyLoadingValueHolderFactory();
                    $initializer = function (
                        & $wrappedObject,
                        LazyLoadingInterface $proxy,
                        $method,
                        array $parameters,
                        & $initializer
                    ) use ($propertyValue) {
                        $initializer = null;
                        if ($propertyValue instanceof ArrayCollection) {
                            $items = array();
                            foreach ($propertyValue as $item) {
                                $items[] = $this->proxyManager->transform($item);
                            }
                            $wrappedObject = new ArrayCollection($items);
                        } else {
                            $wrappedObject = $this->proxyManager->transform($propertyValue);
                        }

                        return true;
                    };

                    if ($propertyValue instanceof Collection) {
                        $objSrcData[$key] = $factory->createProxy(
                            get_class($propertyValue),
                            $initializer
                        );
                    } else {
                        $objSrcData[$key] = $factory->createProxy(
                            $this->annotationReader->getProxyTargetClass(get_class($propertyValue)),
                            $initializer
                        );
                    }
                }
            }
            $objDest = $this->proxyManager->instantiate($proxyClass);

            /**
             * Throw Exceptions
             */
            $objSrcClass = $this->annotationReader->getDoctrineProxyResolver()->unwrapDoctrineProxyClass(get_class($objSrc));
            if (!$objDest instanceof $objSrcClass) {
                throw new \Exception(
                    'The proxy target class should extend the underlying data object.  Proxy Class: ' . $proxyClass
                );
            }

            if (!$this->isProxy($objDest)) {
                throw new \Exception(
                    'The proxy target class should use the Proxy trait.  Proxy Class: ' . $proxyClass
                );
            }

            /**
             * Hydrate the data
             */
            $this->hydrate($objSrcData, $objDest);

            /**
             * Sync Properties
             */
            $syncProperties = $this->annotationReader->getProxySyncedProperties($this->class);
            if ($syncProperties == Constants::SYNC_PROPERTIES_ALL) {
                $syncProperties = array_keys($objSrcData);
            }

            /**
             * Sync Methods
             */
            $syncMethods = $this->annotationReader->getProxySyncMethods($this->class);
            if ($syncMethods == Constants::SYNC_METHODS_ALL) {
                $syncMethods = array();
                foreach (array_keys($objSrcData) as $property) {
                    $syncMethods[] = Inflector::camelize('set_' . $property);
                }
                foreach ($associations as $associationKey => $association) {
                    $associationKey = Inflector::singularize($associationKey);
                    $associationKeyPlural = Inflector::pluralize($associationKey);
                    $syncMethods[] = Inflector::camelize('add_' . $associationKey);
                    $syncMethods[] = Inflector::camelize('remove_' . $associationKey);
                    $syncMethods[] = Inflector::camelize('set_' . $associationKeyPlural);
                    $syncMethods[] = Inflector::camelize('set_' . $associationKey);
                }
            } elseif ($syncMethods == Constants::SYNC_METHODS_NONE) {
                $syncMethods = array();
            }

            /**
             * Set properties on proxied object
             */
            PropertyAccess::set($objDest, 'dataObject', $objSrc);
            PropertyAccess::set($objDest, 'transformer', $this);
            PropertyAccess::set($objDest, 'syncProperties', $syncProperties);
            PropertyAccess::set($objDest, 'syncMethods', $syncMethods);

            /**
             * Attach Interceptors
             */
            $factory = new Factory();
            $proxy = $factory->createProxy($objDest, array());
            foreach ($syncMethods as $syncMethod) {
                $proxy->setMethodSuffixInterceptor(
                    $syncMethod,
                    function ($proxy, $instance) use ($syncMethod) {
                        $syncMethods = PropertyAccess::get($instance, 'syncMethods');
                        if (is_array($syncMethods) && in_array($syncMethod, $syncMethods)) {
                            $instance->syncData();
                        }
                    }
                );
            }

            return $proxy;

        }

        return $objSrc;
    }

    /**
     * Extracts parameters from object and returns an array
     *
     * @param $obj
     * @return array
     */
    public function extract($obj)
    {
        return $this->hydrator->extract($obj);
    }

    /**
     * Hydrates an object using an array of values
     *
     * @param array $data
     * @param $obj
     * @return object
     */
    public function hydrate(array $data, $obj)
    {
        return $this->hydrator->hydrate($data, $obj);
    }

    /**
     * Transfer the data stored in one object to another
     * @param $objSrc
     * @param $objDest
     * @param array|int|string $properties
     * @return object
     * @throws \Exception
     */
    public function transfer($objSrc, $objDest, $properties = Constants::SYNC_PROPERTIES_ALL)
    {
        $srcData = $this->extract($objSrc);
        $destData = $this->extract($objDest);

        if ($properties == Constants::SYNC_PROPERTIES_ALL) {
            $properties = array_keys($srcData);
        }

        if (!is_array($properties)) {
            throw new \Exception('Properties should be either Constants::SYNC_PROPERTIES_ALL or an array');
        }

        $newValues = $destData;
        foreach ($destData as $key => $value) {
            if (in_array($key, $properties)) {
                $newValues[$key] = $srcData[$key];
            }
        }

        return $this->hydrate($newValues, $objDest);
    }

    /**
     * Checks if the target object has the Proxy trait
     *
     * @param $obj
     * @return bool
     */
    protected function isProxy($obj)
    {
        if (array_key_exists('Mindgruve\Gordo\Traits\ProxyTrait', class_uses($obj))) {
            return true;
        }

        return false;
    }

}