<?php

namespace Mindgruve\Gordo\Domain;

trait EntityProxyTrait
{
    protected $entity;

    /**
     * @var Hydrator
     */
    protected $hydrator;

    public function getEntity()
    {
        return $this->entity;
    }

    public function syncEntity()
    {
        $this->hydrator->transfer($this, $this->entity);
    }


}