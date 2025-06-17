<?php

namespace App\Repository;

use App\Entity\SectionAgenda;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionAgenda|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionAgenda|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionAgenda[]    findAll()
 * @method SectionAgenda[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionAgendaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionAgenda::class);
    }

//    /**
//     * @return SectionAgenda[] Returns an array of SectionAgenda objects
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
    public function findOneBySomeField($value): ?SectionAgenda
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
