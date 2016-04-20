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
    protected $reader;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param $class
     */
    public function __construct($class, MetaDataReader $reader, Factory $factory)
    {
        $this->class = $class;
        $this->reader = $reader;
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

    public function buildDomainModel($objSrc)
    {
        $data = $this->extract($objSrc);
        $domainAnnotations = $this->reader->getDomainAnnotations($this->class);

        if ($domainAnnotations) {

            $domainModelClass = $domainAnnotations->domainModel;
            $entityAnnotations = $this->reader->getEntityAnnotations($this->class);
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