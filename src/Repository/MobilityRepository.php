<?php

namespace App\Repository;

use App\Entity\Mobility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mobility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mobility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mobility[]    findAll()
 * @method Mobility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MobilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mobility::class);
    }


    public function findOne($value)
    {
        return $this->createQueryBuilder('m')
            ->where('m.id = :value')->setParameter('value', $value)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
