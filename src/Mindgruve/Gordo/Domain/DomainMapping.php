<?php

namespace Mindgruve\Gordo\Domain;

/**
 * @Annotation
 * @Target("CLASS")
 */
class DomainMapping
{

    /**
     * @var string
     */
    public $domainModel;

}