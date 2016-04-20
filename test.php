<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$loader = include_once(__DIR__ . '/vendor/autoload.php');

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src"), $isDevMode);
// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/db.sqlite',
);

// obtaining the entity manager
$em = EntityManager::create($conn, $config);
$cmf = $em->getMetadataFactory();
var_dump($cmf->getMetadataFor('Poncho\Examples\Encryption\Message'));exit;
////$mr = new \Poncho\Doctrine\MetaDataReader($em);
////$config = $mr->getMetaData('Poncho\Examples\Encryption\Attachment');
////$config = $mr->getMetaData('Poncho\Examples\Encryption\Message');
//
//
//$message = new \Poncho\Examples\Encryption\Message();
//$message->setEmail('ksimpson@mindgruve.com');
//
//
//$proxyManager = new \Poncho\ProxyManager();
//$messageProxy = $proxyManager->buildProxy($message);
//var_dump($messageProxy->getEmail());