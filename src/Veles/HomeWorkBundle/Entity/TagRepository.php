<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /*public function findAllOrderedByIdLimit()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT t FROM VelesHomeWorkBundle:Tag t ORDER BY t.id DESC')
            ->getResult();
    }*/
}
