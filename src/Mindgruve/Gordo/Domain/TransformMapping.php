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

}