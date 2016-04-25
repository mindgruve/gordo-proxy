<?php

namespace Mindgruve\Gordo\Annotations;

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
    public $syncProperties = array();

    /**
     * @var array
     */
    public $syncListeners = array();
}