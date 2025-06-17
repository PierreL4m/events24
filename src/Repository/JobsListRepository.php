<?php

namespace App\Repository;

use App\Entity\JobsList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobsList|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobsList|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobsList[]    findAll()
 * @method JobsList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobsListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobsList::class);
    }

    // /**
    //  * @return JobsList[] Returns an array of JobsList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JobsList
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
