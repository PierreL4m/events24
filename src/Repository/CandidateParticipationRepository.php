<?php

namespace App\Repository;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\ExposantUser;
use App\Entity\Slots;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\EventType;
use EasyRdf\Literal\Integer;

/**
 * @method CandidateParticipation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateParticipation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateParticipation[]    findAll()
 * @method CandidateParticipation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidateParticipation::class);
    }

    public function findOneByEventType(EventType $event_type): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
        ->join('c.event', 'e')
        ->join('e.type', 't')
        ->andWhere('t.id = :event_type_id')
        ->setParameters(array('event_type_id'=> $event_type->getId()))
        ->getQuery()
        ->setMaxResults(1)
        ->getOneOrNullResult()
        ;
    }
    public function findByEvent(Event $event): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
            ->join('c.event', 'e')
            ->andWhere('e.id = :event')
            ->setParameters(array('event'=> $event->getId()))
            ->getQuery()
            ->getResult()
            ;
    }
    public function findOneById(int $id): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameters(array('id'=> $id))
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }
    public function findNumberBySlots($slots)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.slot = :slot')
            ->andWhere('c.status = 2')
            ->setParameters(array('slot'=> $slots->getId()))
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findUnslots($event)
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.slot is null')
            ->andWhere('c.event = :event')
            ->setParameters(array('event'=> $event->getId()))
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findLastByCandidate(CandidateUser $candidate): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
        ->join('c.candidate', 'ca')
        ->andWhere('ca.id = :ca_id')
        ->setParameters(array('ca_id'=> $candidate->getId()))
        ->orderBy('c.createdAt', 'DESC')
        ->getQuery()
        ->setMaxResults(1)
        ->getOneOrNullResult()
        ;
    }

    public function findAllByCandidate(CandidateUser $candidate): ?array
    {
        return $this->createQueryBuilder('c')
            ->join('c.candidate', 'ca')
            ->andWhere('ca.id = :ca_id')
            ->setParameters(array('ca_id'=> $candidate->getId()))
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByCandidateAndEvent(CandidateUser $candidate, Event $event): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
            ->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->andWhere('e.id = :event_id')
            ->andWhere('ca.id = :ca_id')
            ->setParameters(array('event_id'=> $event->getId(),'ca_id'=> $candidate->getId()))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCandidateAndEventAndSlots(CandidateUser $candidate, Event $event, Slots $slots): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
            ->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->andWhere('e.id = :event_id')
            ->andWhere('ca.id = :ca_id')
            ->andWhere('c.slot = :slot')
            ->setParameters(array('event_id'=> $event->getId(),'ca_id'=> $candidate->getId(),'slot'=> $slots->getId()))
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findConfirmed(Event $event)
    {
        return $this->createQueryBuilder('cp')
            ->join('cp.candidate', 'c')
            ->join('cp.event', 'e')
            ->select('c.phone')
            ->where('e.id = :id')
            ->andWhere('cp.status = 2')
            ->setParameter('id', $event->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEventAndExposant(Event $event, ExposantUser $user, $filter)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.event', 'e')
            ->join('c.candidateComments','co')
            ->join('co.organizationParticipation', 'p')
            ->join('p.responsable','r')
            ->andWhere('e.id = :event_id')
            ->andWhere('r.id = :r_id')
            ->setParameters(array('event_id'=> $event->getId(),'r_id'=> $user->getId()));

       switch ($filter) {
            case 'favorite':
                $qb->andWhere('co.favorite = 1');
                break;
            case 'like':
                $qb->andWhere('co.like = 1');
                break;
            case 'dislike':
                $qb->andWhere('co.like = -1');
                break;
            case 'nolike':
                $qb->andWhere('co.like = 0');
                break;
            default:
                break;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEventAndInputQuery(Event $event, $search)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.event', 'e')
            ->join('c.candidate', 'ca')
        ;

        if ($search && $search != 'null'){
            $qb->orwhere('LOWER(ca.email) like :search')
               ->orWhere('LOWER(ca.firstname) like :search')
               ->orWhere('LOWER(ca.lastname) like :search')
               ->setParameter('search' , strtolower($search).'%')
            ;
        }
        $qb->andWhere('e.id = :id')
            ->setParameter('id' , $event->getId())
            ->orderBy('ca.lastname', 'ASC')

        ;

        return $qb->getQuery()->getResult();
    }

    public function findByEventAndEmail(Event $event, $email)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->where('ca.email = :email')
            ->setParameter('email' , $email)
            ->andWhere('e.id = :id')
            ->setParameter('id' , $event->getId())
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByEventAndEmailOrId(Event $event, $info)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->where('ca.email = :email')
            ->orWhere('c.id = :id')
            ->setParameter('email' , $info)
            ->setParameter('id' , $info)
            ->andWhere('e.id = :event')
            ->setParameter('event' , $event->getId())
        ;
        return $qb->getQuery()->getOneOrNullResult();
    }


    public function findOldFromDays(CandidateUser $candidate, $days)
    {
        $date_now = new \Datetime();
        $date = new \Datetime();
        date_sub($date,date_interval_create_from_date_string($days." days"));

        $qb = $this->createQueryBuilder('c')
            ->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->andWhere('e.date >= :date')
            ->andWhere('ca.id = :id')
            ->setParameter('id' , $candidate->getId())
            ->setParameter('date' , $date->format('Y-m-d%'))
            ->andWhere('e.date < :date_now')
            ->setParameter('date_now', $date_now->format('Y-m-d%'))
            ->orderBy('e.date', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
    public function findFuture(CandidateUser $candidate)
    {
        $date_now = new \Datetime();
        $qb = $this->createQueryBuilder('c');
        $qb->join('c.event', 'e')
            ->join('c.candidate', 'ca')
            ->where('e.date >= :date_now')
            ->andWhere('ca.id = :id')
            ->setParameter('id' , $candidate->getId())
            ->setParameter('date_now' , $date_now->format('Y-m-d%'))
            ->orderBy('e.date', 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

     public function findRecallByEvent(Event $event)
    {
       $qb = $this->createQueryBuilder('c');
        $qb->join('c.event', 'e')
        ->join('c.candidate', 'ca')
        ->join('c.status', 's')
        ->where('ca.mailingRecall = true')
        ->andWhere('s.slug = :slug')
        ->andWhere('e.id = :id')
        ->setParameter('id' , $event->getId())
        ->setParameter('slug', 'confirmed')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Get confirmed participations with no invitation
     * @return CandidateParticipation[]
     */
    public function findNoInvitationFutureEvents()
    {
        return $this->createQueryBuilder('p')
        ->join('p.event', 'e')
        ->join('p.status', 's')
        ->where('e.date >= :now')
        ->andWhere('p.invitationPath is null')
        ->andWhere('s.slug = :slug')
        ->setParameter('now', new \DateTime())
        ->setParameter('slug', 'confirmed')
        ->getQuery()
        ->getResult()
        ;
    }

//    /**
//     * @return CandidateParticipation[] Returns an array of CandidateParticipation objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CandidateParticipation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
