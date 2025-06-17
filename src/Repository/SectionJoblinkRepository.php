<?php

namespace App\Repository;

use App\Entity\SectionJoblink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionJoblink|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionJoblink|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionJoblink[]    findAll()
 * @method SectionJoblink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionJoblinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionJoblink::class);
    }

//    /**
//     * @return SectionJoblink[] Returns an array of SectionJoblink objects
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
    public function findOneBySomeField($value): ?SectionJoblink
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
