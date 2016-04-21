<?php

namespace Mindgruve\Gordo\Examples\Encryption\Proxies;

use Mindgruve\Gordo\Examples\Encryption\Entities\Attachment;

class AttachmentProxy extends Attachment
{

    public function getRand()
    {
        return rand(1,100);
    }

}