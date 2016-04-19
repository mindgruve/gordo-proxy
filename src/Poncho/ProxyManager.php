<?php

namespace Poncho;

use ProxyManager\Factory\AccessInterceptorValueHolderFactory;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\GhostObjectInterface;
use ProxyManager\Proxy\LazyLoadingInterface;

class ProxyManager
{


    public function __construct()
    {


    }



    public function buildProxy($obj)
    {
        $factory = new AccessInterceptorValueHolderFactory();
        return $factory->createProxy($obj, array(), array('getEmail' => function($c){
           $c->setEmail('simpkevin@gmail.com');
        }));

    }
}