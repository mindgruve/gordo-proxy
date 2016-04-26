<?php

namespace Mindgruve\Gordo\Annotations;

use Mindgruve\Gordo\Proxy\ProxyConstants;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Proxy
{

    /**
     * @var string
     */
    public $target;

    /**
     * @var array
     */
    public $syncProperties = ProxyConstants::SYNC_PROPERTIES_ALL;

    /**
     * @var array
     */
    public $syncMethods = ProxyConstants::SYNC_METHODS_NONE;
}