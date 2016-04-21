<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GeneratedHydrator\Configuration;

class DomainModelFactory
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
    protected $metaDataReader;

    /**
     * @var ProxyFactory
     */
    protected $factory;

    /**
     * @var
     */
    protected $hydrator;

    /**
     * @var array
     */
    protected $loaders = array();

    /**
     * @param $class
     */
    public function __construct(
        $class,
        EntityManagerInterface $em,
        ProxyFactory $proxyFactory = null,
        AnnotationReader $metaDataReader = null
    ) {
        $this->em = $em;
        if (!$metaDataReader) {
            $metaDataReader = new AnnotationReader($em);
        }

        $this->class = $class;
        $this->metaDataReader = $metaDataReader;
        $this->proxyFactory = $proxyFactory;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
    }

    /**
     * @return string
     */
    public function getDomainModelClass()
    {
        $domainModelClass = $this->class;
        $domainAnnotations = $this->metaDataReader->getDomainAnnotations($this->class);

        if ($domainAnnotations) {

            $domainModelClass = $domainAnnotations->domainModel;
        }

        return $domainModelClass;
    }

    /**
     * @param $objSrc
     * @return object
     */
    public function buildDomainModel($objSrc)
    {
        $data = $this->hydrator->extract($objSrc);
        $domainModelClass = $this->getDomainModelClass();
        if ($domainModelClass != $this->class) {

            $entityAnnotations = $this->metaDataReader->getEntityAnnotations($this->class);
            $associations = $entityAnnotations->getAssociationMappings();

            foreach ($associations as $key => $association) {
                if (isset($data[$key])) {
                    $collection = $data[$key];
                    $items = array();
                    foreach ($collection as $item) {
                        if ($this->proxyFactory) {
                            $item = $this->proxyFactory->createProxy($item);
                        }
                        $items[] = $item;
                    }
                    $data[$key] = new ArrayCollection($items);
                }
            }
            $objDest = $this->instantiate($domainModelClass);

            return $this->hydrator->hydrate($data, $objDest);
        }

        return $objSrc;
    }

    /**
     * @param DependencyFactoryInterface $loader
     * @return $this
     */
    public function registerDependencyLoader(DependencyFactoryInterface $loader)
    {
        $this->loaders[] = $loader;
        return $this;
    }

    /**
     * @param $domainModelClass
     * @return object
     */
    protected function instantiate($domainModelClass)
    {
        foreach ($this->loaders as $loader) {

            /**
             * @var DependencyFactoryInterface $loader
             */
            if ($loader->supports($domainModelClass)) {
                return $loader->buildDomainModel($domainModelClass);
            }
        }

        return new $domainModelClass();

    }

}