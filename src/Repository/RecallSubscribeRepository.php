<?php

namespace App\Repository;

use App\Entity\RecallSubscribe;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecallSubscribe|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecallSubscribe|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecallSubscribe[]    findAll()
 * @method RecallSubscribe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecallSubscribeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecallSubscribe::class);
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('rs')
            ->orderBy('rs.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findByEvent($event)
    {
        $query = $this->createQueryBuilder('rs')
            ->where('rs.event = :event')
            ->setParameters(array('event'=> $event))
            ->getQuery()
            ->getResult();
            return $query;
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
