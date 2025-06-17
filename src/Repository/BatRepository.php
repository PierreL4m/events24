<?php

namespace App\Repository;

use App\Entity\Bat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bat[]    findAll()
 * @method Bat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bat::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('b')
            ->where('b.something = :value')->setParameter('value', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
