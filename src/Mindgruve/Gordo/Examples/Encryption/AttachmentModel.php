<?php

namespace Mindgruve\Gordo\Examples\Encryption;

class AttachmentModel extends Attachment
{

    public function getRand()
    {
        return rand(1,100);
    }

}