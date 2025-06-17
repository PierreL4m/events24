<?php

namespace App\Repository;

use App\Entity\ParticipationSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipationSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipationSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipationSite[]    findAll()
 * @method ParticipationSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipationSite::class);
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
