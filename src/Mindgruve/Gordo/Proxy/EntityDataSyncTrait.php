<?php

namespace Mindgruve\Gordo\Proxy;

trait EntityDataSyncTrait
{
    protected $entity;

    /**
     * @var array
     */
    protected $syncedProperties = array();

    /**
     * @var Hydrator
     */
    protected $hydrator;

    /**
     * @return object
     */
    protected function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sync the fields from the proxy --> entity
     *
     * @param array $properties
     */
    public function syncToEntity(array $properties = null)
    {
        if (!$properties) {
            $properties = $this->syncedProperties;
        }
        $this->hydrator->transfer($this, $this->entity, $properties);
    }

    /**
     * Sync the fields from entity --> proxy
     *
     * @param array $properties
     */
    public function syncFromEntity(array $properties = null)
    {
        if (!$properties) {
            $properties = $this->syncedProperties;
        }
        $this->hydrator->transfer($this->entity, $this, $properties);
    }


}