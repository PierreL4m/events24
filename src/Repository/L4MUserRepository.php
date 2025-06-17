<?php

namespace App\Repository;

use App\Entity\L4MUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method L4MUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method L4MUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method L4MUser[]    findAll()
 * @method L4MUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class L4MUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, L4MUser::class);
    }

    public function getAdminEmails()
    {
        return $this->createQueryBuilder('u')
            ->select('u.email')
            ->where('u.sendPassword = :value ')->setParameter('value', true)
            ->getQuery()
            ->getResult()
        ;
    }
//    /**
//     * @return L4MUser[] Returns an array of L4MUser objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?L4MUser
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
