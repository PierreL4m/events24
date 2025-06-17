<?php

namespace App\Repository;

use App\Entity\RhUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RhUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method RhUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method RhUser[]    findAll()
 * @method RhUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RhUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RhUser::class);
    }

//    /**
//     * @return RhUser[] Returns an array of RhUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RhUser
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
