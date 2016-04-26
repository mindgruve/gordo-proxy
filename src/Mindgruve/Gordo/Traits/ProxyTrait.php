<?php

namespace Mindgruve\Gordo\Traits;

use Mindgruve\Gordo\Proxy\Hydrator;
use Mindgruve\Gordo\Proxy\ProxyConstants;

trait ProxyTrait
{
    protected $dataObject;

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
    protected function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * Sync the fields
     * If $syncDirection ==  ProxyConstants::UPDATE_ENTITY    proxy --> entity
     * If $syncDirection ==  ProxyConstants::UPDATE_PROXY   entity --> proxy
     *
     * @param $syncDirection
     * @param $properties
     */
    public function syncData(
        $syncDirection = ProxyConstants::UPDATE_ENTITY,
        $properties = ProxyConstants::SYNC_PROPERTIES_DEFAULT
    ) {
        if ($properties == ProxyConstants::SYNC_PROPERTIES_DEFAULT) {
            $properties = $this->syncProperties;
        } elseif ($properties == ProxyConstants::SYNC_PROPERTIES_NONE) {
            $properties = array();
        }

        if ($syncDirection == ProxyConstants::UPDATE_ENTITY) {
            $this->hydrator->transfer($this, $this->dataObject, $properties);
        } elseif ($syncDirection == ProxyConstants::UPDATE_PROXY) {
            $this->hydrator->transfer($this->dataObject, $this, $properties);
        }
    }
}