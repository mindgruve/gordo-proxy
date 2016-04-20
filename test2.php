<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Register the ORM Annotations in the AnnotationRegistry



$message = new \Mindgruve\Gordo\Examples\Encryption\Message();
$message->setEmail('ksimpson@mindgruve.com');
$message->setDate(new \DateTime());
$message->setMessage('woot');
$attachment = new \Mindgruve\Gordo\Examples\Encryption\Attachment();
$message->setAttachments(new \Doctrine\Common\Collections\ArrayCollection(array($attachment)));


// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/src"), $isDevMode);
$conn = array(
    'driver' => 'pdo_sqlite',
    'path'   => __DIR__ . '/db.sqlite',
);
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config);

$metaDataReader = new \Mindgruve\Gordo\Domain\MetaDataReader(new \Doctrine\Common\Annotations\SimpleAnnotationReader(), $entityManager);
$domainFactory = new \Mindgruve\Gordo\Domain\Factory($metaDataReader);
$messageModel = $domainFactory->buildDomainModel($message);
