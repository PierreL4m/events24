<?php

namespace App\Repository;

use App\Entity\Origin;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql\Year;
/**
 * @method Origin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Origin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Origin[]    findAll()
 * @method Origin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OriginRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Origin::class);
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('o')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findById(int $id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('j')
            ->where('j.something = :value')->setParameter('value', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
