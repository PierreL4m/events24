<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EmailTypeRepository;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }
    public function getJobs($participation_id)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->getQuery()
            ->getResult();

        return $query;
    }
    public function getByOrganization($organization)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation','p')
            ->join('p.organization','o')
            ->where('o.id = :ido')
            ->setParameter('ido', $organization->getId())
            ->getQuery()
            ->getResult();
        return $query;
    }
    public function getJobByParticipation($participation_id)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->where('p.id = :idParticipation')
            ->setParameters(array('idParticipation' => $participation_id))
            ->getQuery()
            ->getResult();

        return $query;
    }
    public function getNbJobByContractAndParticipation($participation_id)
    {
        $query = $this->createQueryBuilder('j')
            ->select('ct.name, count(j.id) AS nbJobs')
            ->join('j.participation', 'p')
            ->join('j.contractType', 'ct')
            ->where('p.id = :idParticipation')
            ->setParameters(array('idParticipation' => $participation_id))
            ->groupBy('j.contractType')
            ->addOrderBy('nbJobs', 'DESC')
            ->getQuery()
            ->getResult();

        return $query;
    }
    public function getByParticipation($participation)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->where('p.id = :id')
            ->setParameters(array('id' => $participation))
            ->getQuery()
            ->getResult();
        return $query;
    }
    public function getByEvent($event)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->join('p.event', 'e')
            ->where('e.id = :id')
            ->setParameters(array('id' => $event))
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function getJobByEvent($event_id)
    {
        $query = $this->createQueryBuilder('j')
            ->join('j.participation', 'p')
            ->join('p.event', 'e')
            ->where('e.id = :idEvent')
            ->orWhere('e.parentEvent = :idParentEvent')
            ->setParameter('idEvent',$event_id)
            ->setParameter('idParentEvent',$event_id)
            ->getQuery()
            ->getResult();

        return $query;
    }
    public function getByOrganizationAndYear($participation)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("395 days"));
        $query = $this->createQueryBuilder('j')
            ->join('j.organization','o')
            ->join('j.participation','p')
            ->join('p.event','e')
            ->where('o.id = :ido')
            ->andWhere('p.id != :idp')
            ->andwhere('e.date > :date')
            ->setParameter('ido', $participation->getOrganization()->getId())
            ->setParameter('idp', $participation->getId())
            ->setParameter('date', $date)
        ;
        return $query;
    }
    public function getJobsFlux()
    {
        $date = date('Y-m-d');
        $query = $this->createQueryBuilder('j')
        ->join('j.participation', 'p')
        ->join('p.event', 'e')
        ->andwhere('e.date >= :date')
        ->andwhere('e.online <= :date')
        ->setParameter('date', $date)
        ->getQuery()
        ->getResult();
        return $query;
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('j')
            ->where('j.something = :value')->setParameter('value', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
