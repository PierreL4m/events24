<?php

namespace App\Repository;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\ClientUser;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\User;
use App\Entity\CandidateParticipationComment;
use App\Repository\ParticipationCompanySimpleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipationRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class);
    }

    public function checkClass(Participation $p, $repo)
    {
        $pReflectionClass = new \ReflectionClass(get_class($p));
        $p_class_name = $pReflectionClass->getShortName();

        $rReflectionClass = new \ReflectionClass(get_class($repo));
        $r_class_name = $rReflectionClass->getShortName();

        if (str_replace('Repository', '', $r_class_name) != $p_class_name){
            throw new \Exception('Cannot call '.get_class($this).'->getChildMissingData('.get_class($p).')');
        }
    }
    public function findByOrganizationQuery($id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.organization', 'o')
            ->join('p.event','e')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->orderBy('e.date', 'DESC')
            ->getQuery()
        ;
    }

    public function findAllEventsParticipations($id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->where('e.id = :id')
            ->orWhere('e.parentEvent = :parentEventId')
            ->setParameter('id', $id)
            ->setParameter('parentEventId', $id)
            ->getQuery()
            ->getResult();
    }

    public function findByOrganizationAndEvent(Organization $organization, Event $event)
    {
        $id = $organization->getId();
        $e_id = $event->getId();

        return $this->createQueryBuilder('p')
            ->join('p.organization', 'o')
            ->join('p.event','e')
            ->where('o.id = :id')
            ->andWhere('e.id = :e_id')
            ->setParameters(array('id'=> $id, 'e_id' => $e_id))
            ->getQuery()
            ->getResult();
        ;
    }

    public function findLast($nb)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findById($nb)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameters(array('id' => $nb))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getRandomByEvent($event_id)
    {
        $query = $this->createQueryBuilder('p')
                    ->addSelect('RAND() as HIDDEN rand')
                    ->addOrderBy('rand')
                    ->join('p.event', 'e')
                    ->where('e.id = :id')
                    ->setParameters(array('id' => $event_id))
                    ->orderBy('p.premium', 'DESC')
                    ->getQuery()
                    ->getResult();

        return $query;
    }
    public function getOneParticipation($participation_id)
    {
        $query = $this->createQueryBuilder('p')
                    ->addSelect('RAND() as HIDDEN rand')
                    ->addOrderBy('rand')
                    ->where('p.id = :idParticipation')
                    ->setParameters(array('idParticipation' => $participation_id))
                    ->getQuery()
                    ->getResult();

        return $query;
    }

    public function getLastParticipationByOrgaOrUser(User $user, Organization $organization)
    {

        $qb = $this->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->join('p.organization','o')
            ->where('p.responsable = :id')->setParameter('id', $user->getId())
            ->orWhere('p.organization = :idOrga')->setParameter('idOrga', $organization->getId())
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
        ;

        if ($user instanceof ExposantUser || $user instanceof L4MUser){
            $qb->join('p.responsable' , 'u');
        }
        elseif ($user instanceof ExposantScanUser){
            $qb->join('o.exposantScanUser' , 'u');
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastParticipationByOrga(Organization $organization)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("395 days"));
        $currentDate = new \DateTime();
        $qb = $this->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->join('p.organization','o')
            ->where('e.date > :date')
            ->andWhere('e.date < :currentDate')
            ->andWhere('p.organization = :idOrga')
            ->setParameters(array('idOrga' => $organization->getId(), 'date' => $date, 'currentDate' => $currentDate))
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(24)
        ;

        return $qb
            ->getQuery()
            ->getResult();
    }
    
    public function getRandomPremiumByEvent($event_id)
    {
        $query = $this->createQueryBuilder('p')
            ->addSelect('RAND() as HIDDEN rand')
            ->addOrderBy('rand')
            ->join('p.event', 'e')
            ->where('p.premium = 1')
            ->andWhere('e.id = :idEvent')
            ->andWhere('p.pub is not NULL')
            ->andWhere('p.pubMobile is not NULL')
            ->setParameters(array('idEvent' => $event_id))
            ->getQuery()
            ->getResult();

        return $query;
    }

    public function getOrderedByEvent($event_id)
    {
        $query = $this->createQueryBuilder('p')
                    ->join('p.event', 'e')
                    ->where('e.id = :id')
                    ->setParameters(array('id' => $event_id))
                    ->orderBy('p.companyName', 'ASC')
                    ->getQuery()
                    ->getResult();

        return $query;
    }

    public function findByEvent($event)
    {
        $query = $this->createQueryBuilder('p')
                    ->join('p.event', 'e')
                    ->where('e.id = :id')
                    ->setParameters(array('id' => $event->getId()))
                    ->orderBy('p.standNumber', 'ASC')
                    ->getQuery()
                    ->getResult();

        return $query;
    }

    public function getParticipationsQuery(Organization $organization)
    {
        return $this->createQueryBuilder('p')
            ->join('p.organization' , 'o')
            ->join('p.event', 'e')
            ->where('o.id = :id')->setParameter('id', $organization->getId())
            ->orderBy('e.date', 'DESC')
        ;
    }

    public function getLastParticipation(User $user)
    {

        $qb = $this->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->join('p.organization','o')
            ->where('p.responsable = :id')->setParameter('id', $user->getId())
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
        ;

        if ($user instanceof ExposantUser || $user instanceof L4MUser){
            $qb->join('p.responsable' , 'u');
        }
        elseif ($user instanceof ExposantScanUser){
            $qb->join('o.exposantScanUser' , 'u');
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getNextParticipations(Organization $organization)
    {
        return $this->createQueryBuilder('p')
            ->join('p.organization' , 'o')
            ->join('p.event', 'e')
            ->where('o.id = :id')
            ->andWhere('e.date > :date')
            ->setParameters(array('id' => $organization->getId(), 'date' => date('Y-m-d H:i:s')))
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $query;
    }

    public function getPrevious(Organization $organization)
    {
        return $this->createQueryBuilder('p')
            ->join('p.organization', 'o')
            ->join('p.event','e')
            ->where('o.id = :id')
            ->setParameter('id', $organization->getId())
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }

    // this is not verified
    public function getPreviousSameType(Organization $organization, Participation $participation)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->join('p.organization', 'o')
            ->join('p.event','e')
            ->where('o.id = :id')
            ->andWhere($qb->expr()->isInstanceof('p', get_class($participation)))
            ->setParameters(array('id'=> $organization->getId()))
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }


    public function getNotLoggedQuery(Event $event)
    {

        $qb = $this->createQueryBuilder('p');

        return $qb
            ->leftJoin('p.timestamp', 't')
            ->join('p.event', 'e')
            ->andwhere('e.id = :id')
            ->setParameters(array('id' => $event->getId()))
            ->orderBy('p.companyName', 'ASC');

    }
    public function getValidatedQuery(Event $event)
    {
        return $this->createQueryBuilder('p3')
            ->join('p3.timestamp', 't3')
            ->where('t3.updated is null')   ;
    }
    public function getNotLogged(Event $event)
    {
        return $this->getNotLoggedQuery($event)
            ->andWhere('t.updated is null or p.recall = 0')
            ->getQuery()->getResult();
    }

    public function getCommonMissingDatas(Event $event)
    {
        $qb = $this->getNotLoggedQuery($event)->select('p.id')->getDql();
        $qb2 = $this->createQueryBuilder('p2');

        return $qb2
            ->join('p2.event', 'e2')
            ->leftjoin('p2.sites', 's')
            ->leftjoin('p2.logo','l')
            ->where('p2.companyName is NULL')
            ->where('p2.presentation is NULL')
            ->orWhere('s is NULL')
            ->orWhere('s.url is NULL')
            ->orWhere('p2.cp is NULL')
            ->orWhere('p2.city is NULL')
            ->orWhere('p2.logo is NULL')
            ->orWhere('l.path is NULL')
            ->andWhere('e2.id = :id')
            ->andWhere($qb2->expr()->notIn('p2.id', $qb))
            ->setParameter('id', $event->getId())
            ->getQuery()
            ->getResult();
    }
    public function getMissingDatas(Event $event)
    {
        if (!$event->getParticipations()){
            return null;
        }
        $missings = $this->getCommonMissingDatas($event);
        $em = $this->getEntityManager();

        foreach ($event->getParticipations() as $p) {

            $missing = $em->getRepository(get_class($p))->getChildMissingDatas($p);
            if($missing and !in_array($missing,$missings, TRUE)){
                array_push($missings, $missing);
            }
        }

        return $missings;
    }

    public function getMissingResponsables(Event $event)
    {
        return $this->createQueryBuilder('p')
            ->join('p.event','e')
            ->where('e.id = :id')
            ->andWhere('p.responsable is null')
            ->setParameter('id', $event->getId())
            ->orderBy('p.companyName', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMissingStandNumber(Event $event)
    {
        return $this->createQueryBuilder('p')
            ->join('p.event','e')
            ->where('e.id = :id')
            ->andWhere('p.standNumber is null')
            ->setParameter('id', $event->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMissingTechGuide(Event $event)
    {
        return $this->createQueryBuilder('p')
            ->join('p.event','e')
            ->where('e.id = :id')
            ->andWhere('p.standType is null')
            ->setParameter('id', $event->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByClientUserAndEvent(ClientUser $client_user, Event $event)
    {
        $id = $client_user->getId();
        $e_id = $event->getId();

        $qb = $this->createQueryBuilder('p')
            ->join('p.event','e')
        ;

        switch (get_class($client_user)) {
            case ExposantUser::class :
                $qb->join('p.responsable', 'u') ;
                break;
            case ExposantScanUser::class:
                $qb->join('p.organization', 'o')
                    ->join('o.exposantScanUser', 'u') ;
                break;

            default:
                throw new \Exception(get_class($client_user).' is not handled in this function');
                break;
        }

        $qb->where('u.id = :id')
            ->andWhere('e.id = :e_id')
            ->setParameters(array('id'=> $id, 'e_id' => $e_id))
        ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findByCandidateParticipation(CandidateParticipation $candidate_participation)
    {
        $id = $candidate_participation->getId();


        return $this->createQueryBuilder('p')
            ->join('p.candidateComments', 'cc')
            ->join('cc.candidateParticipation', 'cp')
            ->join('cp.candidate', 'c')
            ->where('cp.id = :id')
            ->setParameters(array('id'=> $id))
            ->getQuery()
            ->getResult();
        ;
    }

    public function findTodayByClientUser(ClientUser $user)
    {
        $date_min = new \Datetime(date('Y-m-d')." 00:00:00");
        $date_max = new \Datetime(date('Y-m-d')." 23:59:59");

        $qb = $this->createQueryBuilder('p')
            ->join('p.event', 'e');

        if($user instanceof ExposantUser){
            $qb->join('p.responsable', 'u');
        }
        elseif ($user instanceof ExposantScanUser) {

            $qb->join('p.organization', 'o')
               ->join('o.exposantScanUser', 'u')
            ;
        }
        else{
            throw new \Exception(get_class($user).' is not valid');
        }
        $qb->where('e.date BETWEEN :date_min AND :date_max')
            ->andwhere('u.id = :id')
            ->setParameters(array('date_min' => $date_min, 'date_max' => $date_max, 'id' => $user->getId() ));

        return $qb->getQuery()->getResult() ;
    }

    public function findWithPubByEvent(Event $event)
    {
        $e_id = $event->getId();

        return $this->createQueryBuilder('p')
            ->join('p.event', 'e')
            ->where('p.pub is not null')
            ->andWhere('e.id = :e_id')
            ->setParameters(array('e_id' => $e_id))
            ->getQuery()
            ->getResult();
        ;
    }

    public function findByEventAndCandidate($event, $idCandidate)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateComments','cp')
            ->join('cp.candidateParticipation','cd')
            ->andWhere('c.event = :event_id')
            ->andWhere('cd.candidate = :idCandidate')
            ->setParameters(array('event_id'=> $event,'idCandidate'=> $idCandidate));

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
