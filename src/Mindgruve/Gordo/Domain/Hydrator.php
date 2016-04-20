<?php

namespace Mindgruve\Gordo\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use GeneratedHydrator\Configuration;

class Hydrator
{
    protected $class;

    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @var MetaDataReader
     */
    protected $metaDataReader;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param $class
     */
    public function __construct($class, MetaDataReader $metaDataReader, Factory $factory)
    {
        $this->class = $class;
        $this->metaDataReader = $metaDataReader;
        $this->factory = $factory;

        $config = new Configuration($class);
        $hydratorClass = $config->createFactory()->getHydratorClass();
        $this->hydrator = new $hydratorClass();
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
     *
     * @param $objSrc
     * @param $objDest
     * @return object
     */
    public function transfer($objSrc, $objDest)
    {
        $data = $this->extract($objSrc);

        return $this->hydrate($data, $objDest);
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
        $data = $this->extract($objSrc);

        $domainModelClass = $this->getDomainModelClass();

        if ($domainModelClass != $this->class) {

            $entityAnnotations = $this->metaDataReader->getEntityAnnotations($this->class);
            $associations = $entityAnnotations->getAssociationMappings();

            foreach ($associations as $key => $association) {
                if (isset($data[$key])) {
                    $collection = $data[$key];
                    $items = array();
                    foreach ($collection as $item) {
                        $items[] = $this->factory->buildDomainModel($item);
                    }
                    $data[$key] = new ArrayCollection($items);
                }
            }
            $objDest = new $domainModelClass();

            return $this->hydrate($data, $objDest);
        }

        return $objSrc;
    }
}