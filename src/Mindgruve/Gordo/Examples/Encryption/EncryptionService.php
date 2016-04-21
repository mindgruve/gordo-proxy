<?php

namespace Mindgruve\Gordo\Examples\Encryption;

class EncryptionService
{

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
    public function decrypt($message)
    {
        return openssl_decrypt($message, $this->encryptionKey, $this->encryptionMethod);
    }

    /**
     * @param $message
     * @return $this
     */
    public function encrypt($message)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->encryptionMethod));

        return openssl_encrypt($message, $this->encryptionMethod, $this->encryptionKey, false, $iv);
    }


}