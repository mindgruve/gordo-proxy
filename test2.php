<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Mindgruve\Gordo\Examples\Encryption\Message;
use Mindgruve\Gordo\Examples\Encryption\Attachment;
use Mindgruve\Gordo\Domain\MetaDataReader;
use Mindgruve\Gordo\Domain\Factory\ProxyFactory;
use Mindgruve\Gordo\Domain\Factory\ModelFactory;
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

$metaDataReader = new MetaDataReader($entityManager);
$proxyFactory = new ProxyFactory($metaDataReader);
$domainFactory = new ModelFactory('Mindgruve\Gordo\Examples\Encryption\Message', $metaDataReader, $proxyFactory);
$messageModel = $domainFactory->buildDomainModel($message);

$attachments = $messageModel->getAttachments();
foreach ($attachments as $attachment) {
    echo $attachment->getRand();
}