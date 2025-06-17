<?php

namespace App\Repository;

use App\Entity\EventSimple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventSimple|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventSimple|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventSimple[]    findAll()
 * @method EventSimple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventSimpleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventSimple::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('e')
            ->where('e.something = :value')->setParameter('value', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
