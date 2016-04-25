<?php

namespace Mindgruve\Gordo\Proxy;

/**
 * @Annotation
 * @Target("CLASS")
 */
class ProxyTransform
{

    /**
     * @var string
     */
    public $target;

    /**
     * @var bool
     */
    public $syncAuto = true;

    /**
     * @var array
     */
    public $syncedProperties = array();

    /**
     * @var array
     */
    public $syncListeners = array();
}