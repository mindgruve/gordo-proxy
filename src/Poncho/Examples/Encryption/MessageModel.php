<?php

namespace Poncho\Examples\Encryption;

/**
 * @Entity
 */
class MessageModel extends Message
{
    protected $encryptionManager;

    public function __construct(EncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public function getMessage()
    {
        return $this->encryptionManager->decrypt($this->message);
    }

    public function setMessage($message)
    {
        $this->message = $this->encryptionManager->encrypt($message);
        return $this;
    }

}