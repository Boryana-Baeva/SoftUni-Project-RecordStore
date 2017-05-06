<?php

namespace RecordStoreBundle\Repository;

/**
 * PromotionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PromotionRepository extends \Doctrine\ORM\EntityRepository
{

    public function fetchBiggestGeneralPromotion()
    {
        $qb = $this->createQueryBuilder('p');

        $today = new \DateTime();

        $qb->select('p.percent')
            ->where($qb->expr()->lte('p.start', ':today'))
            ->andWhere($qb->expr()->gte('p.end', ':today'))
            ->andWhere($qb->expr()->isNull('p.category'))
            ->setParameter(':today', $today->format('Y-m-d'))
            ->orderBy('p.percent', 'DESC')
            ->setMaxResults(1);


        $query = $qb->getQuery();

        if ($query->getOneOrNullResult() !== null) {
            return $query->getSingleScalarResult();
        }
        return 0;
    }

    public function fetchCategoriesPromotions()
    {
        $qb = $this->createQueryBuilder('p');

        $today = new \DateTime();

        $qb->select(['MAX(p.percent) as percent', 'c.id'])
            ->join('p.category', 'c')
            ->where($qb->expr()->lte('p.start', ':today'))
            ->andWhere($qb->expr()->gte('p.end', ':today'))
            ->andWhere($qb->expr()->isNotNull('p.category'))
            ->setParameter(':today', $today->format('Y-m-d'))
            ->groupBy('c')
            ->orderBy('p.percent', 'DESC');

        $results = $qb->getQuery()->getResult();

        $promotions = [];
        foreach ($results as $promotion) {
            $promotions[(int)$promotion['id']] = (int)$promotion['percent'];
        }
        return $promotions;

    }

}
