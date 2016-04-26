<?php

namespace Mindgruve\Gordo\Annotations;

use Mindgruve\Gordo\Proxy\Constants;

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
    public $syncProperties = Constants::SYNC_PROPERTIES_ALL;

    /**
     * @var array
     */
    public $syncMethods = Constants::SYNC_METHODS_NONE;
}