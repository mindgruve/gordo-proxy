<?php

namespace Mindgruve\Gordo\Examples\Encryption;

use Mindgruve\Gordo\Domain\DependencyFactoryInterface;

class AttachmentFactory implements DependencyFactoryInterface
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
     * @param $domainModelClass
     * @return object
     */
    public function buildDomainModel($domainModelClass)
    {
        $encryptionKey = uniqid();

        return new AttachmentModel($encryptionKey);
    }


}