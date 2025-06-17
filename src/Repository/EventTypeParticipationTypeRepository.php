<?php

namespace App\Repository;

use App\Entity\EventTypeParticipationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventTypeParticipation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTypeParticipation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTypeParticipation[]    findAll()
 * @method EventTypeParticipation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTypeParticipationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTypeParticipationType::class);
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
