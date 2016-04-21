<?php

namespace Mindgruve\Gordo\Domain;

/**
 * @Annotation
 * @Target("CLASS")
 */
class TransformMapping
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