<?php

namespace App\Repository;

use App\Entity\PartnerType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PartnerType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerType[]    findAll()
 * @method PartnerType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerType::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.something = :value')->setParameter('value', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
