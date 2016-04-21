<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GeneratedHydrator\Configuration;

class EntityDecorator
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
        AnnotationReader $annotationReader = null
    ) {
        $this->em = $em;
        if (!$annotationReader) {
            $annotationReader = new AnnotationReader($em);
        }

        if(!$proxyFactory){
            $proxyFactory = new ProxyFactory($em, $annotationReader);
        }

        $this->class = $class;
        $this->annotationReader = $annotationReader;
        $this->proxyFactory = $proxyFactory;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
    }

    /**
     * @param $objSrc
     * @return object
     */
    public function decorate($objSrc)
    {
        $data = $this->hydrator->extract($objSrc);
        $domainModelClass = $this->annotationReader->getProxyModelClass(get_class($objSrc));
        if ($domainModelClass != $this->class) {

            $entityAnnotations = $this->annotationReader->getEntityAnnotations($this->class);
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
     * @param LoaderInterface $loader
     * @return $this
     */
    public function registerLoader(LoaderInterface $loader)
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
             * @var LoaderInterface $loader
             */
            if ($loader->supports($domainModelClass)) {
                return $loader->buildDomainModel($domainModelClass);
            }
        }

        return new $domainModelClass();

    }

}