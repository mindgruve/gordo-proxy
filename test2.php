<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

use Mindgruve\Gordo\Examples\Encryption\Message;
use Mindgruve\Gordo\Examples\Encryption\Attachment;
use Mindgruve\Gordo\Domain\ProxyFactory;
use Mindgruve\Gordo\Domain\DomainModelFactory;
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

$proxyFactory = new ProxyFactory($entityManager);
$domainFactory = new DomainModelFactory('Mindgruve\Gordo\Examples\Encryption\Message', $entityManager, $proxyFactory);
$domainFactory->registerDependencyLoader(new \Mindgruve\Gordo\Examples\Encryption\AttachmentFactory());
$messageModel = $domainFactory->buildDomainModel($message);

$attachments = $messageModel->getAttachments();
foreach ($attachments as $attachment) {
    echo $attachment->getRand().PHP_EOL;
}