<?php

namespace Mindgruve\Gordo\Examples\Encryption\Proxies;

use Mindgruve\Gordo\Examples\Encryption\EncryptionService;
use Mindgruve\Gordo\Examples\Encryption\Entities\Message;
use Mindgruve\Gordo\Proxy\EntityDataSyncTrait;

class MessageProxy extends Message
{

    use EntityDataSyncTrait;

    /**
     * @var EncryptionService
     */
    protected $encryptionService;

    /**
     * @param EncryptionService $encryptionService
     */
    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->encryptionService->decrypt($this->message);
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $this->encryptionService->encrypt($message);

        return $this;
    }
}