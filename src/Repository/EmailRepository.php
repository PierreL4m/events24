<?php

namespace App\Repository;

use App\Entity\Email;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function findByEventAndSlug(Event $event, $slug)
    {
        return $this->createQueryBuilder('e')
            ->join('e.event', 'ev')
            ->join('e.emailType', 'et')
            ->where('et.slug = :slug')
            ->andWhere('ev.id = :id')
            ->setParameters(array('slug' => $slug, 'id' => $event->getId()))
            ->orderBy('e.sent', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    // public function findBySomething($value)
    // {
    //     return $this->createQueryBuilder('e')
    //         ->where('e.something = :value')->setParameter('value', $value)
    //         ->orderBy('e.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    
}
