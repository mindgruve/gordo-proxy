<?php

namespace Mindgruve\Gordo\Traits;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Proxy\ProxyConstants;

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
     * Sync the fields
     * If $syncDirection ==  ProxyConstants::SYNC_FROM_PROXY    proxy --> entity
     * If $syncDirection ==  ProxyConstants::SYNC_FROM_ENTITY   entity --> proxy
     *
     * @param $properties
     * @param $syncDirection
     */
    public function syncEntity(
        $properties = ProxyConstants::SYNC_DEFAULT_PROPERTIES,
        $syncDirection = ProxyConstants::SYNC_FROM_PROXY
    ) {
        if ($properties == ProxyConstants::SYNC_DEFAULT_PROPERTIES) {
            $properties = $this->syncProperties;
        }

        if ($syncDirection == ProxyConstants::SYNC_FROM_PROXY) {
            $this->hydrator->transfer($this, $this->entity, $properties);
        } elseif ($syncDirection == ProxyConstants::SYNC_FROM_ENTITY) {
            $this->hydrator->transfer($this->entity, $this, $properties);
        }
    }
}