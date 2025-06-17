<?php

namespace App\Repository;

use App\Entity\SpecBase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SpecBase|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecBase|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecBase[]    findAll()
 * @method SpecBase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecBaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecBase::class);
    }


    public function findOne()
    {
        return $this->createQueryBuilder('s')            
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
    public function findDefault()
    {
        return $this->createQueryBuilder('s')
        ->where('s.use_default = :true')
        ->orderBy('s.id', 'DESC')
        ->setMaxResults(1)
        ->setParameter('true', true)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }
    
    
    // public function findBySomething($value)
    // {
    //     return $this->createQueryBuilder('s')
    //         ->where('s.something = :value')->setParameter('value', $value)
    //         ->orderBy('s.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    
}
