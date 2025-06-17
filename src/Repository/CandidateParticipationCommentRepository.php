<?php

namespace App\Repository;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\ClientUser;
use App\Entity\Event;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CandidateUser;

/**
 * @method CandidateParticipationComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateParticipationComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateParticipationComment[]    findAll()
 * @method CandidateParticipationComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateParticipationCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidateParticipationComment::class);
    }

    public function findOneByParticipations(Participation $participation, CandidateParticipation $c_participation): ?CandidateParticipationComment
    {
        return $this->createQueryBuilder('c')
            ->join('c.candidateParticipation', 'cp')
            ->Join('c.organizationParticipation', 'p')
            ->andWhere('cp.id = :cp_id')
            ->andWhere('p.id = :p_id')
            ->setParameters(array('cp_id' => $c_participation->getId(), 'p_id' => $participation->getId()))
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }

    public function findByEventAndExposant(Event $event, ClientUser $user, $filter=null, $param = null)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipation','cp')
            ->join('cp.candidate','ca')
            ->join('cp.event', 'e')
            ->join('c.organizationParticipation', 'p')
        ;
        switch (get_class($user)) {
            case ExposantUser::class :
                $qb->join('p.responsable','u');
                break;

            case ExposantScanUser::class :
                $qb->join('p.organization','o')
                    ->join('o.exposantScanUser', 'u');
                break;

            default:
                throw new \Exception(get_class($user).' not handled');
                break;
        }

        $qb->andWhere('e.id = :event_id')
            ->addOrderBy('c.favorite', 'DESC')
            ->addOrderBy('c.like' , 'DESC')

            ->addOrderBy('ca.lastname','ASC')
            ->andWhere('u.id = :u_id')
            ->setParameters(array('event_id'=> $event->getId(),'u_id'=> $user->getId()));

        switch ($filter) {
            case 'favorite':
                $qb->andWhere('c.favorite = 1');
                break;
            case 'like':
                $qb->andWhere('c.like = 1');
                break;
            case 'dislike':
                $qb->andWhere('c.like = -1');
                break;
            case 'nolike':
                $qb->andWhere('c.like = 0')
                    ->andWhere('c.favorite is null or c.favorite = 0')
                ;
                break;
            default:
                break;
        }

        if($param){
            $qb->andWhere('ca.lastname like :param or ca.firstname like :param')
                ->setParameter('param', '%'.$param.'%');
        }
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByEvent(Event $event)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipation','cp')
            ->join('cp.event', 'e')
            ->andWhere('e.id = :event_id')
            ->setParameters(array('event_id'=> $event->getId()));

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByEventAndFilter(Event $event)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipation','cp')
            ->join('cp.event', 'e')
            ->andWhere('e.id = :event_id')
            ->setParameters(array('event_id'=> $event->getId()));

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByEventAndCandidate(Event $event, CandidateUser $candidate)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipation','cp')
            ->join('cp.event', 'e')
            ->andWhere('e.id = :event_id')
            ->andWhere('cp.candidate = :idCandidate')
            ->setParameters(array('event_id'=> $event,'idCandidate'=> $candidate->getId()));

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
    public function findById(CandidateParticipationComment $comment): ?CandidateParticipationComment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $comment->getId())
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
//    /**
//     * @return CandidateParticipationComment[] Returns an array of CandidateParticipationComment objects
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
    public function findOneBySomeField($value): ?CandidateParticipationComment
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
