<?php

namespace Mindgruve\Gordo\Domain\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use GeneratedHydrator\Configuration;
use Mindgruve\Gordo\Domain\MetaDataReader;

class ModelFactory
{

    /**
     * @var
     */
    protected $class;

    /**
     * @var MetaDataReader
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
     * @param $class
     */
    public function __construct($class, MetaDataReader $metaDataReader, ProxyFactory $proxyFactory)
    {
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
                        $items[] = $this->proxyFactory->createProxy($item);
                    }
                    $data[$key] = new ArrayCollection($items);
                }
            }
            $objDest = new $domainModelClass();

            return $this->hydrator->hydrate($data, $objDest);
        }

        return $objSrc;
    }

}