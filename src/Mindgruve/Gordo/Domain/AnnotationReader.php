<?php
/**
 * Created by PhpStorm.
 * User: ksimpson
 * Date: 4/20/16
 * Time: 1:44 PM
 */

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Annotations\Reader as ReaderInterface;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

class AnnotationReader
{

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var CachedReader|ReaderInterface
     */
    protected $reader;

    /**
     * @param EntityManagerInterface $em
     * @param ReaderInterface $reader
     * @param array $namespaces
     * @param CacheProvider $cacheProvider
     */
    public function __construct(
        EntityManagerInterface $em,
        ReaderInterface $reader = null,
        array $namespaces = array('Doctrine\ORM\Mapping', 'Mindgruve\Gordo\Domain'),
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
     * @param $class
     * @return null | TransformMapping
     */
    public function getTransformAnnotations($class)
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof TransformMapping) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param $class
     * @return null|string
     */
    public function getEntityTransformTargetClass($class)
    {
        $annotations = $this->getTransformAnnotations($class);
        if ($annotations) {
            return $annotations->target;
        }

        return null;
    }

    /**
     * @param $class
     * @return array|null
     */
    public function getEntityTransformationSyncedProperties($class){
        $annotations = $this->getTransformAnnotations($class);
        if ($annotations) {
            return $annotations->syncedProperties;
        }

        return null;
    }

    /**
     * @param $class
     * @return bool
     */
    public function getEntitySyncAuto($class){
        $annotations = $this->getTransformAnnotations($class);
        if ($annotations) {
            return $annotations->syncAuto;
        }

        return false;
    }

    /**
     * @param $class
     * @return array|null
     */
    public function getEntitySyncListeners($class){
        $annotations = $this->getTransformAnnotations($class);
        if ($annotations) {
            return $annotations->syncListeners;
        }

        return null;
    }

    /**
     * @param $class
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getEntityAnnotations($class)
    {
        return $this->em->getClassMetadata($class);
    }
}