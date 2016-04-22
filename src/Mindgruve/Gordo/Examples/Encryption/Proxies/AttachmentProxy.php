<?php

namespace Mindgruve\Gordo\Examples\Encryption\Proxies;

use Mindgruve\Gordo\Examples\Encryption\Entities\Attachment;
use Mindgruve\Gordo\Domain\EntityDataSyncTrait;

class AttachmentProxy extends Attachment
{
    use EntityDataSyncTrait;

}