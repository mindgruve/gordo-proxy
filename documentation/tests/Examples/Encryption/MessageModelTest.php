<?php

namespace Poncho\Tests\Examples\Encyption;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Poncho\Examples\Encryption\EncryptionManager;
use Poncho\Examples\Encryption\MessageModel;

class MessageModelTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryption()
    {

//        $encryptionManager = new EncryptionManager('MCRYPT_BLOWFISH', 'KEY', 'MCRYPT_MODE_CBC', 'iv');
//        $messageModel = new MessageModel($encryptionManager);
//
//        $messageModel->setDate(new \DateTime());
//        $messageModel->setEmail('ksimpson@mindgruve.com');
//        $messageModel->setMessage('howdy');
//
//        $isDevMode = true;
//        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../../../../"), $isDevMode);
//        $conn = array(
//            'driver' => 'pdo_sqlite',
//            'path' => __DIR__ . '/db.sqlite',
//        );
//        $em = EntityManager::create($conn, $config);
//        var_dump($em->getMetadataFactory()->getMetadataFor('Poncho\Examples\Encryption\MessageModel'));
//
//        $em->persist($messageModel);
//        $em->flush();
    }
}