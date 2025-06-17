<?php

namespace App\Repository;

use App\Entity\JoblinkSession;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JoblinkSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method JoblinkSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method JoblinkSession[]    findAll()
 * @method JoblinkSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoblinkSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JoblinkSession::class);
    }

    public function findByEvent($value)
    {
        return $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->where('p.event = :value')
            ->setParameter('value', $value)
            ->orderBy('j.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCandidateParticipation($id)
    {
        return $this->createQueryBuilder('j')
            ->join('j.candidates', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
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
