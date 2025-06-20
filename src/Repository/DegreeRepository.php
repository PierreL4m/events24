<?php

namespace App\Repository;

use App\Entity\Degree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Degree|null find($id, $lockMode = null, $lockVersion = null)
 * @method Degree|null findOneBy(array $criteria, array $orderBy = null)
 * @method Degree[]    findAll()
 * @method Degree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DegreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Degree::class);
    }


    public function findOne($value)
    {
        return $this->createQueryBuilder('d')
            ->where('d.id = :value')->setParameter('value', $value)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
