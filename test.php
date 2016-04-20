<?php

$loader = include_once(__DIR__ . '/vendor/autoload.php');
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
use Doctrine\Common\Annotations\AnnotationReader;

$reader = new AnnotationReader();
var_dump($reader->getClassAnnotations(new ReflectionClass('Mindgruve\Gordo\Examples\Encryption\Message')));