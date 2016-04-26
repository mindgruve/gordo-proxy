<?php

namespace Mindgruve\Gordo\Annotations;

use Mindgruve\Gordo\Proxy\ProxyConstants;

/**
 * @Annotation
 * @Target("CLASS")
 */
class EntityProxy
{

    /**
     * @var string
     */
    public $target;

    /**
     * @var bool
     */
    public $syncAuto = false;

    /**
     * @var array
     */
    public $syncProperties = ProxyConstants::SYNC_ALL_PROPERTIES;

    /**
     * @var array
     */
    public $syncListeners = ProxyConstants::SYNC_ALL_LISTENERS;
}