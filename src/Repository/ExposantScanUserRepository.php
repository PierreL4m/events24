<?php

namespace App\Repository;

use App\Entity\ExposantScanUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExposantScanUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExposantScanUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExposantScanUser[]    findAll()
 * @method ExposantScanUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExposantScanUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExposantScanUser::class);
    }


//    /**
//     * @return ExposantScanUser[] Returns an array of ExposantScanUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExposantScanUser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
