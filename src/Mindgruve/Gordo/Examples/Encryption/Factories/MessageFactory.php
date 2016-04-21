<?php

namespace Mindgruve\Gordo\Examples\Encryption\Factories;

use Mindgruve\Gordo\Domain\FactoryInterface;
use Mindgruve\Gordo\Examples\Encryption\EncryptionService;
use Mindgruve\Gordo\Examples\Encryption\Proxies\MessageProxy;

class MessageFactory implements FactoryInterface
{
    /**
     * @param $proxyModelClass
     * @return bool
     */
    public function supports($proxyModelClass)
    {
        if ($proxyModelClass == 'Mindgruve\Gordo\Examples\Encryption\Proxies\MessageProxy') {
            return true;
        }

        return false;
    }

    /**
     * @param $modelProxyClass
     * @return object
     */
    public function build($modelProxyClass)
    {
        $encryptionKey = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
        $encryptionMethod = 'aes-128-cbc';

        $encryptionService = new EncryptionService($encryptionKey, $encryptionMethod);

        return new MessageProxy($encryptionService);
    }
}