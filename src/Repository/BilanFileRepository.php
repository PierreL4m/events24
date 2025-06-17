<?php

namespace App\Repository;

use App\Entity\BilanFile;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BilanFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method BilanFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method BilanFile[]    findAll()
 * @method BilanFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BilanFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BilanFile::class);
    }

    
    public function findByEvent(Event $event)
    {
        return $this->createQueryBuilder('b')
            ->join('b.event', 'e')
            ->join('b.bilanFileType', 'bt')
            ->where('e.id = :id')->setParameter('id', $event->getId())
            ->orderBy('bt.label', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByEventAndType(Event $event, $type)
    {
        return $this->createQueryBuilder('b')
            ->join('b.event', 'e')
            ->join('b.bilanFileType', 'bt')
            ->where('e.id = :id')
            ->andWhere('bt.type = :type')
            ->setParameters(['id'=> $event->getId(), 'type' => $type])
            ->orderBy('b.name', 'ASC')
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
