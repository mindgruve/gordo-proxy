<?php

namespace Mindgruve\Gordo\Domain;

trait EntityProxyTrait
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
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sync the fields from the proxy --> entity
     */
    public function syncEntity()
    {
        $this->hydrator->transfer($this, $this->entity, $this->syncedProperties);
    }


}