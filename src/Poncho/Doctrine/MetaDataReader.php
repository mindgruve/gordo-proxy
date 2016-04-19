<?php

namespace Poncho\Doctrine;

use Doctrine\ORM\EntityManager;

class MetaDataReader
{

    /**
     * @var EntityManager
     */
    protected $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getMetaData($entity)
    {

        $cmf = $this->em->getMetadataFactory();
        return $cmf->getMetadataFor($entity);
    }


}