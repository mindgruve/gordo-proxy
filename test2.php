<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Mindgruve\Gordo\Examples\Encryption\Entities\Message;
use Mindgruve\Gordo\Examples\Encryption\Entities\Attachment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use Doctrine\ORM\EntityManager;


$isDevMode = true;
$config = DoctrineSetup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src"), $isDevMode);
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/db.sqlite',
);
$entityManager = EntityManager::create($conn, $config);


$message = new Message();
$message->setEmail('ksimpson@mindgruve.com');
$message->setDate(new \DateTime());
$message->setMessage('woot');
$attachment = new Attachment();
$message->setAttachments(new ArrayCollection(array($attachment)));

$proxyManager = new \Mindgruve\Gordo\Proxy\ProxyManager($entityManager);
$proxyManager->registerFactory(new \Mindgruve\Gordo\Examples\Encryption\Factories\MessageFactory());
$messageProxy = $proxyManager->transform($message);

$messageProxy->setMessage('kevin');
$messageProxy->setEmail('test@test.com');
//$messageProxy->setDate(new \DateTime('3/14/2015'));
$messageProxy->syncData();
var_dump($message->getMessage());


$testEntity1 = new \Mindgruve\Gordo\Tests\Entity\TestEntity1();
$testProxy1 = $proxyManager->transform($testEntity1);