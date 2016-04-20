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
use Mindgruve\Gordo\Domain\DomainMapping as DomainAnnotations;

class MetaDataReader
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
        ReaderInterface $reader,
        EntityManagerInterface $em,
        array $namespaces = array('Doctrine\ORM\Mapping', 'Mindgruve\Gordo\Domain'),
        CacheProvider $cacheProvider = null
    ) {

        $this->em = $em;

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
     * @return null | DomainMapping
     */
    public function getDomainAnnotations($class)
    {
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($class));
        foreach ($annotations as $annotation) {
            if ($annotation instanceof DomainMapping) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param $class
     * @return null|string
     */
    public function getDomainModelClass($class)
    {
        $annotations = $this->getDomainAnnotations($class);
        if ($annotations) {
            return $annotations->domainModel;
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