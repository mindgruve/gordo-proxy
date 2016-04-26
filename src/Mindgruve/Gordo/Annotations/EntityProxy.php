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
     * @var string
     */
    public $sync = ProxyConstants::SYNC_MANUAL;

    /**
     * @var array
     */
    public $syncProperties = ProxyConstants::SYNC_PROPERTIES_ALL;

    /**
     * @var array
     */
    public $syncMethods = ProxyConstants::SYNC_METHODS_ALL;
}