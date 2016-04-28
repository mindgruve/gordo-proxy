<?php

namespace Mindgruve\Gordo\Proxy;

class DoctrineProxyResolver
{

    protected $namespaces = array();


    /**
     * @param array $namespaces
     */
    public function __construct(
        array $namespaces = array(
            'ORM'      => 'Proxies',
            'PHPCR'    => 'PHPCRProxies',
            'MongoODM' => 'MongoDBODMProxies',
        )
    ) {
        $this->namespaces = $namespaces;
    }

    /**
     * Checks if class is actually a doctrine class
     *
     * @param $class
     * @return bool
     */
    public function isDoctrineProxy($class)
    {
        foreach ($this->namespaces as $namespace) {
            if (preg_match('/^'.$namespace.'/', $class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the underlying Doctrine Class
     *
     * @param $class
     * @return mixed
     */
    public function unwrapDoctrineProxyClass($class)
    {
        foreach ($this->namespaces as $namespace) {
            if (preg_match('/^'.$namespace.'/', $class)) {
                return str_replace($namespace.'\\__CG__\\','',$class);
            }
        }

        return $class;
    }

}