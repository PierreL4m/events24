<?php

namespace App\Repository;

use App\Entity\PressFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PressFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method PressFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method PressFile[]    findAll()
 * @method PressFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PressFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PressFile::class);
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
