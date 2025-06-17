<?php

namespace App\Repository;

use App\Entity\ExposantUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExposantUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExposantUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExposantUser[]    findAll()
 * @method ExposantUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExposantUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExposantUser::class);
    }

    /**
     * @return ExposantUser[] Returns an array of ExposantUser objects
     */    
    public function findForMailingClient()
    {
        $date = new \Datetime();
        $date->sub(new \DateInterval('P3Y'));
        
        $qb = $this->createQueryBuilder('e')
            ->select('e.firstname')
            ->addSelect('e.lastname')
            ->addSelect('e.email')
            ->leftJoin('e.responsableBises','rb')
            ->addSelect('rb.email as bis_email')
            ->join('e.participations','p')
            ->join('p.event','ev')
            ->join('p.organization','o')
            ->addSelect('o.name')
            ->where('ev.date >= :now')
            ->setParameter('now', $date->format('Y-m-d H:i:s'))
            ->distinct()
            ->orderBy('ev.date', 'ASC')
        ;

        return $qb->getQuery()
            ->getResult();
    }
    
    /**
     * @return ExposantUser[] Returns an array of ExposantUser objects
     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExposantUser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
