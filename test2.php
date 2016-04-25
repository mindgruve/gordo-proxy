<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Mindgruve\Gordo\Examples\Encryption\Entities\Message;
use Mindgruve\Gordo\Examples\Encryption\Entities\Attachment;
use Mindgruve\Gordo\Proxy\ProxyTransformer;
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

$proxyTranformer = new ProxyTransformer('Mindgruve\Gordo\Examples\Encryption\Entities\Message', $entityManager);
$proxyTranformer->registerFactory(new \Mindgruve\Gordo\Examples\Encryption\Factories\MessageFactory());
$messageProxy = $proxyTranformer->transform($message);

$messageProxy->setMessage('kevin');
$messageProxy->setEmail('test@test.com');
//$messageProxy->setDate(new \DateTime('3/14/2015'));
$messageProxy->syncToEntity();
var_dump($message->getMessage());


$testEntity1 = new \Mindgruve\Gordo\Tests\TestEntity1();
$proxyTranformer = new ProxyTransformer('Mindgruve\Gordo\Tests\TestEntity1', $entityManager);
$testProxy1 = $proxyTranformer->transform($testEntity1);
var_dump($testProxy1);exit;