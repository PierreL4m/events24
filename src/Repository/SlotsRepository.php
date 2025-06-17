<?php

namespace App\Repository;

use App\Entity\Slots;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slots|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slots|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slots[]    findAll()
 * @method Slots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slots::class);
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.begin_slot', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findByEvent($event)
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.event = :event')
            ->setParameters(array('event'=> $event))
            ->getQuery()
            ->getResult();
            return $query;
    }
    public function findOne($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameters(array('id'=> $id))
            ->getQuery()
            ->getResult()
            ;
    }
    public function countSlotsInEvent($event)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where('s.event = :id')
            ->setParameters(array('id'=> $event->getId()))
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countSlotsInEventNotFull($event)
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where('s.is_full = 0')
            ->andWhere('s.event = :event')
            ->setParameters(array('event'=> $event->getId()))
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findAllNotFull($event)
    {
        return $this->createQueryBuilder('s')
            ->where('s.maxCandidats > (SELECT COUNT(cp) FROM App\Entity\CandidateParticipation cp WHERE cp.slot = s.id)')
            ->andWhere('s.event = '.$event->getId())
            ->getQuery()
            ->getResult();
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
