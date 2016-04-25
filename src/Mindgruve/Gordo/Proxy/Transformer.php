<?php

namespace Mindgruve\Gordo\Proxy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory as Factory;
use Doctrine\Common\Inflector\Inflector;
use Mindgruve\Gordo\Annotations\AnnotationReader;

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
     * @var
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
        Hydrator $hydrator,
        ProxyManager $proxyManager
    ) {
        $this->em = $em;
        $this->class = $class;
        $this->annotationReader = $annotationReader;
        $this->hydrator = $hydrator;
        $this->proxyManager = $proxyManager;
    }

    /**
     * Transform Entity to a new Object
     * If the target Object has the EntityProxy trait, then it will also generate a proxy class
     *
     * @param $objSrc
     * @return mixed
     * @throws \Exception
     */
    public function transform($objSrc)
    {
        $objSrcData = $this->hydrator->extract($objSrc);
        $entityProxyClass = $this->annotationReader->getProxyTargetClass(get_class($objSrc));

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
            $objDest = $this->proxyManager->instantiate($entityProxyClass);

            if (!$objDest instanceof $objSrc) {
                throw new \Exception('The proxy target class should extend the underlying entity.  Proxy Class: '.$entityProxyClass);
            }

            if (!$this->isEntityProxy($objDest)) {
                throw new \Exception('The proxy target class should use the EntityProxy trait.  Proxy Class: '.$entityProxyClass);
            }

            $this->hydrator->hydrate($objSrcData, $objDest);
            $reflectionClass = new \ReflectionClass($objDest);

            $reflectionProperty = $reflectionClass->getProperty('entity');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $objSrc);
            $reflectionProperty->setAccessible(false);

            $reflectionProperty = $reflectionClass->getProperty('hydrator');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $this->hydrator);
            $reflectionProperty->setAccessible(false);


            $syncProperties = $this->annotationReader->getProxySyncedProperties($this->class);
            if ($syncProperties == array('*')) {
                $syncProperties = array_keys($objSrcData);
            }

            $reflectionProperty = $reflectionClass->getProperty('syncProperties');
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($objDest, $syncProperties);
            $reflectionProperty->setAccessible(false);

            $factory = new Factory();
            $proxy = $factory->createProxy($objDest, array());

            $syncAuto = $this->annotationReader->getProxySyncAuto($this->class);
            if ($syncAuto) {

                $syncedListeners = $this->annotationReader->getProxySyncListeners($this->class);
                if ($syncedListeners == array('*')) {
                    $syncedListeners = array();
                    foreach (array_keys($objSrcData) as $property) {
                        $syncedListeners[] = Inflector::camelize('set_' . $property);
                    }
                    foreach ($associations as $associationKey => $association) {
                        $associationKey = Inflector::singularize($associationKey);
                        $syncedListeners[] = Inflector::camelize('add_' . $associationKey);
                        $syncedListeners[] = Inflector::camelize('remove_' . $associationKey);
                    }
                }

                foreach ($syncedListeners as $syncListener) {
                    $proxy->setMethodSuffixInterceptor(
                        $syncListener,
                        function ($proxy, $instance) {
                            $instance->syncToEntity();
                        }
                    );
                }

            }

            return $proxy;

        }

        return $objSrc;
    }

    /**
     * Checks if the target object has the EntityProxy trait
     *
     * @param $obj
     * @return bool
     */
    protected function isEntityProxy($obj)
    {
        if (array_key_exists('Mindgruve\Gordo\Traits\EntityProxyTrait', class_uses($obj))) {
            return true;
        }

        return false;
    }

}