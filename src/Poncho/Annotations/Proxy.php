<?php

namespace Poncho\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Proxy
{

    /**
     * @var string
     */
    public $viewModel;

    /**
     * @var string
     */
    public $factory;

}