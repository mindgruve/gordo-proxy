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
     * @param ReaderInterface $reader
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
            $reader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
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
     * @return null | ProxyMapping
     */
    public function getDomainAnnotations($class)
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ProxyMapping) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param $class
     * @return null|string
     */
    public function getProxyModelClass($class)
    {
        $annotations = $this->getDomainAnnotations($class);
        if ($annotations) {
            return $annotations->proxy;
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