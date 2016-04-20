<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

$message = new \Mindgruve\Gordo\Examples\Encryption\Message();
$message->setEmail('ksimpson@mindgruve.com');
$message->setDate(new \DateTime());
$message->setMessage('woot');
$attachment = new \Mindgruve\Gordo\Examples\Encryption\Attachment();
$message->setAttachments(new \Doctrine\Common\Collections\ArrayCollection(array($attachment)));

$domainFactory = new \Mindgruve\Gordo\Factory();
$messageModel = $domainFactory->buildDomainModel($message);


var_dump($messageModel->getMessage());