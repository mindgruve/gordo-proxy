<?php

namespace Mindgruve\Gordo\Examples\Encryption;

use Mindgruve\Gordo\Examples\Encryption\Entities\Attachment;

class AttachmentModel extends Attachment
{

    public function getRand()
    {
        return rand(1,100);
    }

}