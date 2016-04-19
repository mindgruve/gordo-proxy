<?php

namespace Poncho\Examples\Encryption;

class EncryptionManager
{

    public function encrypt($plainText)
    {
        return base64_encode($plainText);
    }

    public function decrypt($cipherText)
    {
        return base64_decode($cipherText);
    }
}