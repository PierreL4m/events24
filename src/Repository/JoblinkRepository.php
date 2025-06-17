<?php

namespace App\Repository;

use App\Entity\Joblink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Joblink|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joblink|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joblink[]    findAll()
 * @method Joblink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoblinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joblink::class);
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
