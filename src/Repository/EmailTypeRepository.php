<?php

namespace App\Repository;

use App\Entity\EmailType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailType[]    findAll()
 * @method EmailType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailType::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('e')
            ->where('e.something = :value')->setParameter('value', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
