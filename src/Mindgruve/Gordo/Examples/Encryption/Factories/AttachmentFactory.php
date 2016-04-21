<?php

namespace Mindgruve\Gordo\Examples\Encryption\Factories;

use Mindgruve\Gordo\Domain\LoaderInterface;

class AttachmentFactory implements LoaderInterface
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