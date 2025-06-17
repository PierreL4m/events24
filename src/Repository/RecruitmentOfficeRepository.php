<?php

namespace App\Repository;

use App\Entity\RecruitmentOffice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecruitmentOffice|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecruitmentOffice|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecruitmentOffice[]    findAll()
 * @method RecruitmentOffice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruitmentOfficeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecruitmentOffice::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('r')
            ->where('r.something = :value')->setParameter('value', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
