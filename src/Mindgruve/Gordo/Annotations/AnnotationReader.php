<?php

namespace Mindgruve\Gordo\Annotations;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Annotations\Reader as ReaderInterface;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Mindgruve\Gordo\Proxy\Constants;
use Mindgruve\Gordo\Proxy\DoctrineProxyResolver;

class AnnotationReader
{

    /**
     * Reference to ObjectManager
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var DoctrineProxyResolver
     */
    protected $doctrineProxyResolver;

    /**
     * Reference to Annotation Reader
     * @var CachedReader|ReaderInterface
     */
    protected $reader;

    /**
     * @param ObjectManager $objectManager
     * @param DoctrineProxyResolver $doctrineProxyResolver
     * @param ReaderInterface $reader
     * @param array $namespaces
     * @param CacheProvider $cacheProvider
     */
    public function __construct(
        ObjectManager $objectManager,
        DoctrineProxyResolver $doctrineProxyResolver = null,
        ReaderInterface $reader = null,
        array $namespaces = array('Doctrine\ORM\Mapping', 'Mindgruve\Gordo\Annotations'),
        CacheProvider $cacheProvider = null
    ) {
        $this->objectManager = $objectManager;

        if (!$doctrineProxyResolver) {
            $doctrineProxyResolver = new DoctrineProxyResolver();
        }
        $this->doctrineProxyResolver = $doctrineProxyResolver;

        if (!$reader) {
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
     * Annotations specifically related to Entity Transformation as defined in the Proxy class
     *
     * @param $class
     * @return null | Proxy
     */
    public function getProxyAnnotations($class)
    {
        if ($this->doctrineProxyResolver->isDoctrineProxy($class)) {
            $class = $this->doctrineProxyResolver->unwrapDoctrineProxyClass($class);
        }

        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Proxy) {
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
        if ($this->doctrineProxyResolver->isDoctrineProxy($class)) {
            $class = $this->doctrineProxyResolver->unwrapDoctrineProxyClass($class);
        }

        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            return $annotations->target;
        }

        return null;
    }

    /**
     * Default properties that will be copied over to doctrine data object when syncData is called
     * An empty array is interpreted as all properties
     *
     * @param $class
     * @return array|null
     */
    public function getProxySyncedProperties($class)
    {
        if ($this->doctrineProxyResolver->isDoctrineProxy($class)) {
            $class = $this->doctrineProxyResolver->unwrapDoctrineProxyClass($class);
        }

        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            if ($annotations->syncProperties == array('*')) {
                return Constants::SYNC_PROPERTIES_ALL;
            }

            return $annotations->syncProperties;
        }

        return null;
    }

    /**
     * Methods that will call the dataSync() method
     * An empty array is interpreted as all methods
     *
     * @param $class
     * @return array|null
     */
    public function getProxySyncMethods($class)
    {
        if ($this->doctrineProxyResolver->isDoctrineProxy($class)) {
            $class = $this->doctrineProxyResolver->unwrapDoctrineProxyClass($class);
        }

        $annotations = $this->getProxyAnnotations($class);
        if ($annotations) {
            if ($annotations->syncMethods == array('*')) {
                return Constants::SYNC_METHODS_ALL;
            }

            return $annotations->syncMethods;
        }

        return null;
    }

    /**
     * Doctrine Entity annotations including references
     *
     * @param $class
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getDoctrineAnnotations($class)
    {
        if ($this->doctrineProxyResolver->isDoctrineProxy($class)) {
            $class = $this->doctrineProxyResolver->unwrapDoctrineProxyClass($class);
        }

        return $this->objectManager->getClassMetadata($class);
    }

    public function getDoctrineProxyResolver()
    {
        return $this->doctrineProxyResolver;
    }
}