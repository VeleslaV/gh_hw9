<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GbookRepository extends EntityRepository
{
    public function findAllOrderedById()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT g FROM VelesHomeWorkBundle:Gbook g ORDER BY g.id DESC')
            ->getResult();
    }

    public function findLatestCommentsLimit($limit)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT g FROM VelesHomeWorkBundle:Gbook g ORDER BY g.id DESC')
            ->setMaxResults($limit)
            ->getResult();
    }
}
