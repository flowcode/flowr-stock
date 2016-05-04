<?php

namespace Flower\StockBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RawMaterialRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RawMaterialRepository extends EntityRepository
{
    public function getTotalCount()
    {
        $qb = $this->createQueryBuilder('rm');

        $qb->select('COUNT(rm)');
        $qb->where('rm.enabled = :enabled')->setParameter('enabled', true);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getTotalCountWithouhStock()
    {
        $qb = $this->createQueryBuilder('rm');

        $qb->select('COUNT(rm)');
        $qb->where('rm.stock <= 0');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
