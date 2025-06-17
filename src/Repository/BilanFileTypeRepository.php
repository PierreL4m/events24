<?php

namespace App\Repository;

use App\Entity\BilanFileType;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BilanFileType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BilanFileType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BilanFileType[]    findAll()
 * @method BilanFileType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BilanFileTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BilanFileType::class);
    }

    public function findByEvent(Event $event)
    {
        return $this->createQueryBuilder('bt')
            ->join('bt.bilanFiles','b')
            ->join('b.event', 'e')
            ->where('e.id = :id')->setParameter('id', $event->getId())
            ->groupBy('bt.label')
            ->getQuery()
            ->getResult()
        ;
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
