<?php

namespace Mindgruve\Gordo\Traits;

use Mindgruve\Gordo\Proxy\Constants;
use Mindgruve\Gordo\Proxy\Transformer;

trait ProxyTrait
{
    protected $dataObject;

    /**
     * @var array
     */
    protected $syncProperties = array();

    /**
     * @var Transformer
     */
    protected $transformer;

    /**
     * @return object
     */
    protected function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * Sync the fields
     * If $syncDirection ==  Constants::UPDATE_DATA_OBJECT     proxy --> data object
     * If $syncDirection ==  Constants::UPDATE_PROXY           data object --> proxy
     *
     * @param $syncDirection
     * @param $properties
     */
    public function syncData(
        $syncDirection = Constants::UPDATE_DATA_OBJECT,
        $properties = Constants::SYNC_PROPERTIES_DEFAULT
    ) {
        if ($properties == Constants::SYNC_PROPERTIES_DEFAULT) {
            $properties = $this->syncProperties;
        } elseif ($properties == Constants::SYNC_PROPERTIES_NONE) {
            $properties = array();
        }

        if ($syncDirection == Constants::UPDATE_DATA_OBJECT) {
            $this->transformer->transfer($this, $this->dataObject, $properties);
        } elseif ($syncDirection == Constants::UPDATE_PROXY) {
            $this->transformer->transfer($this->dataObject, $this, $properties);
        }
    }
}