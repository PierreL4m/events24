<?php

namespace App\Repository;

use App\Entity\SectionParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionParticipant[]    findAll()
 * @method SectionParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionParticipant::class);
    }

//    /**
//     * @return SectionParticipant[] Returns an array of SectionParticipant objects
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
    public function findOneBySomeField($value): ?SectionParticipant
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
