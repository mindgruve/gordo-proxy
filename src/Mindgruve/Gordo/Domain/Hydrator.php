<?php

namespace Mindgruve\Gordo\Domain;

use GeneratedHydrator\Configuration;

class Hydrator
{
    protected $class;

    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @param $class
     */
    public function __construct($class)
    {
        $this->class = $class;

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
}