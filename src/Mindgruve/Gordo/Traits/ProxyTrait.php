<?php

namespace Mindgruve\Gordo\Traits;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Proxy\ProxyConstants;

trait ProxyTrait
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
     * Sync the fields
     * If $syncDirection ==  ProxyConstants::SYNC_FROM_PROXY    proxy --> entity
     * If $syncDirection ==  ProxyConstants::SYNC_FROM_ENTITY   entity --> proxy
     *
     * @param $syncDirection
     * @param $properties
     */
    public function syncEntity(
        $syncDirection = ProxyConstants::SYNC_FROM_PROXY,
        $properties = ProxyConstants::SYNC_PROPERTIES_DEFAULT
    ) {
        if ($properties == ProxyConstants::SYNC_PROPERTIES_DEFAULT) {
            $properties = $this->syncProperties;
        } elseif ($properties == ProxyConstants::SYNC_PROPERTIES_NONE) {
            $properties = array();
        }

        if ($syncDirection == ProxyConstants::SYNC_FROM_PROXY) {
            $this->hydrator->transfer($this, $this->entity, $properties);
        } elseif ($syncDirection == ProxyConstants::SYNC_FROM_ENTITY) {
            $this->hydrator->transfer($this->entity, $this, $properties);
        }
    }
}