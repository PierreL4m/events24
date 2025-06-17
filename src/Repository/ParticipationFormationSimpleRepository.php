<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\ParticipationFormationSimple;
use App\Model\ParticipationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipationFormationSimple|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipationFormationSimple|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipationFormationSimple[]    findAll()
 * @method ParticipationFormationSimple[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationFormationSimpleRepository extends ServiceEntityRepository implements ParticipationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipationFormationSimple::class);
    }

    public function getChildMissingDatas(Participation $participation)
    {
        $this->getEntityManager()->getRepository(Participation::class)->checkClass($participation,$this);


        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.description is null')
            ->setParameter('id', $participation->getId())
            ->getQuery()
            ->getOneOrNullResult()
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
