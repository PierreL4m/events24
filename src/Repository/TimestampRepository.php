<?php

namespace App\Repository;

use App\Entity\Timestamp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Timestamp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timestamp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timestamp[]    findAll()
 * @method Timestamp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimestampRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timestamp::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('t')
            ->where('t.something = :value')->setParameter('value', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
