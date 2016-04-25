<?php

namespace Mindgruve\Gordo\Annotations;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Annotations\Reader as ReaderInterface;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

class AnnotationReader
{

    /**
     * Reference to EntityManager
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Reference to Annotation Reader
     * @var CachedReader|ReaderInterface
     */
    protected $reader;

    /**
     * Constructor
     *
     * @param EntityManagerInterface $em
     * @param ReaderInterface $reader
     * @param array $namespaces
     * @param CacheProvider $cacheProvider
     */
    public function __construct(
        EntityManagerInterface $em,
        ReaderInterface $reader = null,
        array $namespaces = array('Doctrine\ORM\Mapping', 'Mindgruve\Gordo\Annotations'),
        CacheProvider $cacheProvider = null
    ) {

        $this->em = $em;

        if(!$reader){
            $reader = new SimpleAnnotationReader();
        }

        foreach ($namespaces as $namespace) {
            $reader->addNamespace($namespace);
        }

        if ($cacheProvider) {
            $reader = new CachedReader($reader, $cacheProvider);
        }

        $this->reader = $reader;
    }

    /**
     * Annotations specifically related to Entity Transformation as defined in the EntityProxy class
     *
     * @param $class
     * @return null | EntityProxy
     */
    public function getProxyAnnotations($class)
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof EntityProxy) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * The target class to be created
     *
     * @param $class
     * @return null|string
     */
    public function getProxyTargetClass($class)
    {
        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            return $annotations->target;
        }

        return null;
    }

    /**
     * Default properties that will be copied over to entity when syncEntity is called
     *
     * @param $class
     * @return array|null
     */
    public function getProxySyncedProperties($class){
        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            return $annotations->syncProperties;
        }

        return null;
    }

    /**
     * Enable/Disable automatic syncing when methods are called on the class
     *
     * @param $class
     * @return bool
     */
    public function getEntitySyncAuto($class){
        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            return $annotations->syncAuto;
        }

        return false;
    }

    /**
     * Methods that will call the entitySync() method
     *
     * @param $class
     * @return array|null
     */
    public function getProxySyncListeners($class){
        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            return $annotations->syncListeners;
        }

        return null;
    }

    /**
     * Doctrine Entity annotations including references
     *
     * @param $class
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getEntityAnnotations($class)
    {
        return $this->em->getClassMetadata($class);
    }
}