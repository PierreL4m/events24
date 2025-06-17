<?php

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\ResponsableBis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResponsableBis|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResponsableBis|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResponsableBis[]    findAll()
 * @method ResponsableBis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponsableBisRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResponsableBis::class);
    }

    public function getResponsableBis(Participation $participation){

        return $this->createQueryBuilder('rb')
            ->select('rb.email')
            ->where('rb.responsable = :id')
            ->setParameters(array('id' => $participation->getResponsable()->getId()))
            ->getQuery()
            ->getResult();
    }
}
