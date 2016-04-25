<?php

namespace Mindgruve\Gordo\Traits;
use Mindgruve\Gordo\Proxy\Hydrator;

trait EntityProxyTrait
{
    protected $entity;

    /**
     * @var array
     */
    protected $syncProperties = array();

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
            $properties = $this->syncProperties;
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
            $properties = $this->syncProperties;
        }
        $this->hydrator->transfer($this->entity, $this, $properties);
    }


}