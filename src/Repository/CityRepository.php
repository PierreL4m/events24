<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findOne($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findByChars($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :value')->setParameter('value', $value.'%')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
     public function findByCharsForAjax($value)
    {
        return $this->createQueryBuilder('c')
            ->select('c.name', 'c.id', 'c.cp')
            ->join('c.country', 'co')
            ->addSelect('co.name as country')
            ->where('c.name LIKE :value')->setParameter('value', $value.'%')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByCp($value)
    {
        return $this->createQueryBuilder('c')
            ->select('c.name', 'c.id', 'c.cp')
            ->where('c.cp LIKE :value')->setParameter('value', $value.'%')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findIdByName($value)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.name = :value')->setParameter('value', $value.'%')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findById($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :value')->setParameter('value', $value.'%')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
