<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /*public function findAllOrderedByIdLimit()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT p FROM VelesHomeWorkBundle:Gbook p ORDER BY p.id DESC')
            ->getResult();
    }*/
}
