<?php

namespace App\Repository;

use App\Entity\SectionSimple;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionSimple|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionSimple|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionSimple[]    findAll()
 * @method SectionSimple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionSimpleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionSimple::class);
    }

//    /**
//     * @return SectionSimple[] Returns an array of SectionSimple objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SectionSimple
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
