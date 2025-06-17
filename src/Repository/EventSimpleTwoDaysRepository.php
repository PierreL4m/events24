<?php

namespace App\Repository;

use App\Entity\EventSimpleTwoDays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventSimpleTwoDays|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventSimpleTwoDays|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventSimpleTwoDays[]    findAll()
 * @method EventSimpleTwoDays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventSimpleTwoDaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventSimpleTwoDays::class);
    }

//    /**
//     * @return EventSimpleTwoDays[] Returns an array of EventSimpleTwoDays objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventSimpleTwoDays
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
