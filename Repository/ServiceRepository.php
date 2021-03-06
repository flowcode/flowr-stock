<?php

namespace Flower\StockBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceRepository extends EntityRepository
{
    public function getTotalCount()
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('COUNT(s)');
        $qb->where('s.enabled = :enabled')->setParameter('enabled', true);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
