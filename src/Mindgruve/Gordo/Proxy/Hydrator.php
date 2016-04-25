<?php

namespace Mindgruve\Gordo\Proxy;

use GeneratedHydrator\Configuration;

class Hydrator
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Zend\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @param $class
     */
    public function __construct($class, AnnotationReader $annotationReader)
    {
        $this->class = $class;
        $this->annotationReader = $annotationReader;

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
     * @param array $properties
     * @return object
     */
    public function transfer($objSrc, $objDest, array $properties = null)
    {
        $srcData = $this->extract($objSrc);
        $destData = $this->extract($objDest);

        if(is_null($properties)){
            $properties = array_keys($srcData);
        }

        $newValues = $destData;
        foreach($destData as $key => $value){
            if(in_array($key, $properties)){
                $newValues[$key] = $srcData[$key];
            }
        }

        return $this->hydrate($newValues, $objDest);
    }
}