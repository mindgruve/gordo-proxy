<?php

namespace Mindgruve\Gordo\Domain;

trait EntityProxyTrait
{
    protected $entity = null;

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity){
        $this->entity = $entity;
    }

}