<?php

namespace App\Repository;

use App\Entity\SectionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SectionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SectionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SectionType[]    findAll()
 * @method SectionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectionType::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
