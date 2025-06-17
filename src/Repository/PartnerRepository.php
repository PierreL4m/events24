<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Partner;
use App\Entity\SectionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }
    
    public function findByEventAndSectionType(Event $event,$section_type_slug)
    {
        return $this->createQueryBuilder('p')
            ->join('p.events','e')
            ->join('p.partnerType','pt')
            ->where('e.id = :id')->setParameter('id', $event->getId())
            ->andWhere('pt.slug = :slug')->setParameter('slug', $section_type_slug)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
