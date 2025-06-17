<?php

namespace App\Repository;

use App\Entity\SectionPartner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionPartner|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionPartner|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionPartner[]    findAll()
 * @method SectionPartner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionPartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionPartner::class);
    }

//    /**
//     * @return SectionPartner[] Returns an array of SectionPartner objects
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
    public function findOneBySomeField($value): ?SectionPartner
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
