<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\Organization;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organization::class);
    }

    public function findAllQuery()
    {
        return $this->createQueryBuilder('o')            
            ->orderBy('o.name', 'ASC')
            ->getQuery()          
        ;
    }
    
    public function findLast($nb)
    {
        return $this->createQueryBuilder('o')            
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchQuery($search)
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.name like :search')
            ->orWhere('o.internalName like :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('o.name', 'ASC');
       

        return $qb->getQuery() ;
        
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('o')
            ->join('o.participations', 'p')
            ->join('p.responsable', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getOneOrNullResult()
        ;
        
    }

    public function findById($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;

    }

    public function findByEvent(Event $event)
    {
        return $this->createQueryBuilder('o')
            ->join('o.participations', 'p')
            ->join('p.event', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $event->getId())
            ->orderBy('o.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        
    }
    // public function findBySomething($event)
    // {
    //     $qb = $er->createQueryBuilder('o');
    //     $qb2 = $er->createQueryBuilder('o2')
    //            ->select('o2.id')
    //            ->join('o2.organizationTypes' ,'ot');
                        


    //             $i=0;
    //             foreach ($event->getOrganizationTypes() as $type) {
    //                 $qb2->orWhere('ot.id = :id'.$i);
    //                 $qb->setParameter('id'.$i, $type->getId());
    //                 $i++;
    //             }

    //             $qb2 = $qb2->getDql();
    //             $qb->where($qb->expr()->in('o.id',$qb2))
    //             ->orderBy('o.name', 'ASC');

    //     return $qb
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('o')
            ->where('o.something = :value')->setParameter('value', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
