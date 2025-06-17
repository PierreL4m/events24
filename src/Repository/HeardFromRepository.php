<?php

namespace App\Repository;

use App\Entity\HeardFrom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HeardFrom|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeardFrom|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeardFrom[]    findAll()
 * @method HeardFrom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeardFromRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeardFrom::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('h')
            ->where('h.something = :value')->setParameter('value', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
