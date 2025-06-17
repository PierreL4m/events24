<?php

namespace App\Repository;

use App\Entity\EventYoutube;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventYoutube|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventYoutube|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventYoutube[]    findAll()
 * @method EventYoutube[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventYoutubeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventYoutube::class);
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
