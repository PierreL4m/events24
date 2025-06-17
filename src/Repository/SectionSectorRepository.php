<?php

namespace App\Repository;

use App\Entity\SectionSector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionSector|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionSector|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionSector[]    findAll()
 * @method SectionSector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionSectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionSector::class);
    }

//    /**
//     * @return SectionSector[] Returns an array of SectionSector objects
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
    public function findOneBySomeField($value): ?SectionSector
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
