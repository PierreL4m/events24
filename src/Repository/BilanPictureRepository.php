<?php

namespace App\Repository;

use App\Entity\BilanPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BilanPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method BilanPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method BilanPicture[]    findAll()
 * @method BilanPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BilanPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BilanPicture::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('b')
            ->where('b.something = :value')->setParameter('value', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
