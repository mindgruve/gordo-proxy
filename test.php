<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

include_once(__DIR__. '/vendor/autoload.php');

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

// obtaining the entity manager
$em = EntityManager::create($conn, $config);

//$mr = new \Poncho\Doctrine\MetaDataReader($em);
//$config = $mr->getMetaData('Poncho\Examples\Encryption\Attachment');
//$config = $mr->getMetaData('Poncho\Examples\Encryption\Message');


$message = new \Poncho\Examples\Encryption\Message();
$message->setEmail('ksimpson@mindgruve.com');


$proxyManager = new \Poncho\ProxyManager();
$messageProxy = $proxyManager->buildProxy($message);
var_dump($messageProxy->getEmail());