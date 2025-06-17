<?php

namespace App\Repository;

use App\Entity\ParticipationDefault;
use App\Entity\Participation;
use App\Model\ParticipationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipationDefault|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipationDefault|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipationDefault[]    findAll()
 * @method ParticipationDefault[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationDefaultRepository extends ServiceEntityRepository implements ParticipationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipationDefault::class);
    }

    public function getChildMissingDatas(Participation $participation)
    {   
        $this->getEntityManager()->getRepository(Participation::class)->checkClass($participation,$this);

        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.presentation is null')
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
