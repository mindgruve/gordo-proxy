<?php

namespace Mindgruve\Gordo\Examples\Encryption\Proxies;

use Mindgruve\Gordo\Examples\Encryption\Entities\Message;
use Mindgruve\Gordo\Domain\EntityProxyTrait;

class MessageProxy extends Message
{

    use EntityProxyTrait;

    /**
     * @var string
     */
    protected $encryptionKey;

    /**
     * @var string
     */
    protected $encryptionMethod;

    /**
     * @param $encryptionKey
     * @param $encryptionMethod
     */
    public function __construct($encryptionKey, $encryptionMethod)
    {
        $this->encryptionKey = $encryptionKey;
        $this->encryptionMethod = $encryptionMethod;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return openssl_decrypt($this->message, $this->encryptionKey, $this->encryptionMethod);
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->encryptionMethod));
        $this->message = openssl_encrypt($message, $this->encryptionMethod, $this->encryptionKey, false, $iv);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEncryptedMessage()
    {
        return $this->message;
    }

}