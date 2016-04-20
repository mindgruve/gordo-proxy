<?php

include_once(__DIR__ . '/vendor/autoload.php');


$data = array(
    'id'          => 1,
    'date'        => new \DateTime(),
    'email'       => 'ksimpson@mindgruve.com',
    'attachments' => null,
    'message'     => '',
);

$config = new \GeneratedHydrator\Configuration('Poncho\Examples\Encryption\Message');
$hydratorClass = $config->createFactory()->getHydratorClass();
$hydrator = new $hydratorClass();
$object = new \Poncho\Examples\Encryption\Message();

$hydrator->hydrate(
    $data,
    $object
);

var_dump($object->getEmail());