<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use Doctrine\Common\Inflector\Inflector;
use Mindgruve\Gordo\Annotations\AnnotationReader;

use GeneratedHydrator\Configuration;

class Transformer
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
     * @param EntityManagerInterface $em
     * @param AnnotationReader $annotationReader
     */
    public function __construct(
        $class,
        EntityManagerInterface $em,
        AnnotationReader $annotationReader,
        ProxyManager $proxyManager
    ) {
        $this->em = $em;
        $this->class = $class;
        $this->annotationReader = $annotationReader;
        $this->proxyManager = $proxyManager;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
    }

    /**
     * Transform Entity to a new Object
     * If the target Object has the Proxy trait, then it will also generate a proxy class
     *
     * @param $objSrc
     * @return mixed
     * @throws \Exception
     */
    public function transform($objSrc)
    {
        $objSrcData = $this->hydrator->extract($objSrc);
        $proxyClass = $this->annotationReader->getProxyTargetClass(get_class($objSrc));

        if ($proxyClass != $this->class) {

            $doctrineAnnotations = $this->annotationReader->getDoctrineAnnotations($this->class);
            $associations = $doctrineAnnotations->getAssociationMappings();

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
            $objDest = $this->proxyManager->instantiate($proxyClass);

            if (!$objDest instanceof $objSrc) {
                throw new \Exception(
                    'The proxy target class should extend the underlying data object.  Proxy Class: ' . $proxyClass
                );
            }

            if (!$this->isProxy($objDest)) {
                throw new \Exception(
                    'The proxy target class should use the Proxy trait.  Proxy Class: ' . $proxyClass
                );
            }

            $this->hydrate($objSrcData, $objDest);
            $reflectionClass = new \ReflectionClass($objDest);

            $reflectionProperty = $reflectionClass->getProperty('dataObject');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $objSrc);
            $reflectionProperty->setAccessible(false);

            $reflectionProperty = $reflectionClass->getProperty('transformer');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $this);
            $reflectionProperty->setAccessible(false);


            $syncProperties = $this->annotationReader->getProxySyncedProperties($this->class);
            if ($syncProperties == Constants::SYNC_PROPERTIES_ALL) {
                $syncProperties = array_keys($objSrcData);
            }

            $reflectionProperty = $reflectionClass->getProperty('syncProperties');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $syncProperties);
            $reflectionProperty->setAccessible(false);

            $factory = new Factory();
            $proxy = $factory->createProxy($objDest, array());

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

            foreach ($syncMethods as $syncMethod) {
                $proxy->setMethodSuffixInterceptor(
                    $syncMethod,
                    function ($proxy, $instance) {
                        $instance->syncData();
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

        if($properties == Constants::SYNC_PROPERTIES_ALL){
            $properties = array_keys($srcData);
        }

        if(!is_array($properties)){
            throw new \Exception('Properties should be either Constants::SYNC_PROPERTIES_ALL or an array');
        }

        $newValues = $destData;
        foreach($destData as $key => $value){
            if(in_array($key, $properties)){
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