<?php

namespace Mindgruve\Gordo\Examples\Encryption\Factories;

use Mindgruve\Gordo\Domain\FactoryInterface;
use Mindgruve\Gordo\Examples\Encryption\Proxies\AttachmentProxy;

class AttachmentFactory implements FactoryInterface
{
    /**
     * @param $domainModelClass
     * @return bool
     */
    public function supports($domainModelClass)
    {
        if ($domainModelClass == 'Mindgruve\Gordo\Examples\Encryption\Attachment') {
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
        $encryptionKey = uniqid();

        return new AttachmentProxy($encryptionKey);
    }


}