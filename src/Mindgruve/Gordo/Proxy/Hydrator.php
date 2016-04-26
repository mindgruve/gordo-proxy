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
     * @param $objSrc
     * @param $objDest
     * @param int|array $properties
     * @return object
     * @throws \Exception
     */
    public function transfer($objSrc, $objDest, $properties = ProxyConstants::SYNC_ALL_PROPERTIES)
    {
        $srcData = $this->extract($objSrc);
        $destData = $this->extract($objDest);

        if($properties == ProxyConstants::SYNC_ALL_PROPERTIES){
            $properties = array_keys($srcData);
        }

        if(!is_array($properties)){
            throw new \Exception('Properties should be either ProxyConstants::SYNC_ALL_PROPERTIES or an array');
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