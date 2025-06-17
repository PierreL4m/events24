<?php

namespace App\Repository;

use App\Entity\OnsiteUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OnsiteUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method OnsiteUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method OnsiteUser[]    findAll()
 * @method OnsiteUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OnsiteUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OnsiteUser::class);
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
//     * @return OnsiteUser[] Returns an array of OnsiteUser objects
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
    public function findOneBySomeField($value): ?OnsiteUser
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
