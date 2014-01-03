<?php

namespace Veles\HomeWorkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    public function findLatestArticlesLimit($limit)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM VelesHomeWorkBundle:Article a ORDER BY a.id DESC')
            ->setMaxResults($limit)
            ->getResult();
    }
    public function findMostViewedArticlesLimit($limit)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT a FROM VelesHomeWorkBundle:Article a ORDER BY a.views DESC')
            ->setMaxResults($limit)
            ->getResult();
    }
}
