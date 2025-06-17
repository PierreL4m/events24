<?php

namespace App\Repository;

use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Slots;
use App\Entity\RecruitmentOffice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\RhUser;
use App\Entity\EventType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @method CandidateUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidateUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidateUser[]    findAll()
 * @method CandidateUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidateUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidateUser::class);
    }

    public function findByEvent(Event $event, $filter = [])
    {
        return $this->findByEventQuery($event, $filter)->getQuery()->getResult();
    }
    public function findByEventUnslots(Event $event, $filter = [])
    {
        return $this->findByEventAndUnslotsQuery($event, $filter)->getQuery()->getResult();
    }
    public function findByEventAndSlots(Event $event,Slots $slots, $filter = [])
    {
        return $this->findByEventAndSlotsQuery($event, $slots, $filter)->getQuery()->getResult();
    }

    /**
     *
     * @param Event $event
     * * @param Slots $slots
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEventAndSlotsQuery(Event $event, Slots $slots,  $filter = [], $orderby = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
        ;

        if (!empty($filter['search'])) {
            $qb->orwhere('c.email like :search')
                ->orWhere('c.firstname like :search')
                ->orWhere('c.lastname like :search')
                ->orWhere('c.phone like :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if(!empty($filter['rh']) && $filter['rh'] instanceof RhUser) {
            if($event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH) {
                $qb->leftJoin('App\Entity\RhUser', 'u')
                    ->leftJoin('u.recruitmentOffice', 'ro')
                    ->leftJoin('cp.status', 's2')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->orWhere('s2.slug = :slug2')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                    ->setParameter('slug2', 'registered')
                ;
            }
            else {
                $qb->join('App\Entity\RhUser', 'u')
                    ->join('u.recruitmentOffice', 'ro')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                ;
            }
        }
        else if(!empty($filter['ro'])){
            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'rh')
            ;
        }
        else if(!empty($filter['l4m'])){

            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'l4m')
            ;
        }
        else {
            if(!empty($filter['user']) && count($filter['user'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('u.id IN (:uid)')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('uid', $filter['user'])
                ;
            }
            if(!empty($filter['origin']) && count($filter['origin'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('c.origin = :uid')
                    ->setParameter('uid', $filter['origin'][0]->getId())
                ;
            }
        }
        if(!empty($filter['status'])) {
            if(is_object($filter['status']) && $filter['status'] instanceof ArrayCollection) {
                $statuses = array();
                foreach($filter['status'] as $status) {
                    $statuses[] = $status->getId();
                }
                $qb->andWhere('cp.status IN (:statuses)')
                    ->setParameter('statuses', $statuses);
            }
            else {
                $qb->join('cp.status', 's')
                    ->andWhere('cp.status = :status or s.slug =:status')
                    ->setParameter('status', $filter['status']);
            }
        }

        if(!empty($filter['mailing_recall'])) {
            $qb->andWhere('c.mailingRecall = true');
        }

        if(!empty($filter['rh_sectors'])) {
            if(is_object($filter['rh_sectors']) && $filter['rh_sectors'] instanceof ArrayCollection) {
                $sectors = array();
                foreach($filter['rh_sectors'] as $sector) {
                    $sectors[] = $sector->getId();
                }
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id IN (:sectors)')
                    ->setParameter('sectors', $sectors);
            }
            else {
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id = :sector')
                    ->setParameter('sector', $filter['rh_sectors']);
            }
        }

        if(array_key_exists('came',$filter) && $filter['came'] == true){
            $qb->andWhere('cp.scannedAt is not null');
        }

        if(!empty($filter['from'])) {

            $qb
                ->andWhere('cp.comesFrom = :from')
                ->setParameter('from', $filter['from']);

        }

        if(!empty($orderby) && count($orderby)) {
            foreach($orderby as $o) {
                switch($o) {
                    case 'name' :
                        $qb->orderBy('c.lastname')->addOrderBy('c.firstname');
                        break;
                    case 'created' :
                        $qb->orderBy('cp.createdAt');
                        break;
                }
            }
        }

        $qb
            ->andWhere('e.id = :id')
            ->andWhere('cp.slot = :slots_id')
            ->setParameter('id', $event->getId())
            ->setParameter('slots_id', $slots->getId())
            //  ->orderBy('c.lastname', 'ASC')
            //   ->orderBy('cp.createdAt')
            ->getQuery()
            ->getResult()
        ;


        return $qb;
    }

    /**
     *
     * @param Event $event
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEventAndUnslotsQuery(Event $event, $filter = [], $orderby = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
        ;

        if (!empty($filter['search'])) {
            $qb->orwhere('c.email like :search')
                ->orWhere('c.firstname like :search')
                ->orWhere('c.lastname like :search')
                ->orWhere('c.phone like :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if(!empty($filter['rh']) && $filter['rh'] instanceof RhUser) {
            if($event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH) {
                $qb->leftJoin('App\Entity\RhUser', 'u')
                    ->leftJoin('u.recruitmentOffice', 'ro')
                    ->leftJoin('cp.status', 's2')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->orWhere('s2.slug = :slug2')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                    ->setParameter('slug2', 'registered')
                ;
            }
            else {
                $qb->join('App\Entity\RhUser', 'u')
                    ->join('u.recruitmentOffice', 'ro')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                ;
            }
        }
        else if(!empty($filter['ro'])){
            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'rh')
            ;
        }
        else if(!empty($filter['l4m'])){

            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'l4m')
            ;
        }
        else {
            if(!empty($filter['user']) && count($filter['user'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('u.id IN (:uid)')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('uid', $filter['user'])
                ;
            }
            if(!empty($filter['origin']) && count($filter['origin'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('c.origin = :uid')
                    ->setParameter('uid', $filter['origin'][0]->getId())
                ;
            }
        }
        if(!empty($filter['status'])) {
            if(is_object($filter['status']) && $filter['status'] instanceof ArrayCollection) {
                $statuses = array();
                foreach($filter['status'] as $status) {
                    $statuses[] = $status->getId();
                }
                $qb->andWhere('cp.status IN (:statuses)')
                    ->setParameter('statuses', $statuses);
            }
            else {
                $qb->join('cp.status', 's')
                    ->andWhere('cp.status = :status or s.slug =:status')
                    ->setParameter('status', $filter['status']);
            }
        }

        if(!empty($filter['mailing_recall'])) {
            $qb->andWhere('c.mailingRecall = true');
        }

        if(!empty($filter['rh_sectors'])) {
            if(is_object($filter['rh_sectors']) && $filter['rh_sectors'] instanceof ArrayCollection) {
                $sectors = array();
                foreach($filter['rh_sectors'] as $sector) {
                    $sectors[] = $sector->getId();
                }
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id IN (:sectors)')
                    ->setParameter('sectors', $sectors);
            }
            else {
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id = :sector')
                    ->setParameter('sector', $filter['rh_sectors']);
            }
        }

        if(array_key_exists('came',$filter) && $filter['came'] == true){
            $qb->andWhere('cp.scannedAt is not null');
        }

        if(!empty($filter['from'])) {

            $qb
                ->andWhere('cp.comesFrom = :from')
                ->setParameter('from', $filter['from']);

        }

        if(!empty($orderby) && count($orderby)) {
            foreach($orderby as $o) {
                switch($o) {
                    case 'name' :
                        $qb->orderBy('c.lastname')->addOrderBy('c.firstname');
                        break;
                    case 'created' :
                        $qb->orderBy('cp.createdAt');
                        break;
                }
            }
        }

        $qb
            ->andWhere('e.id = :id')
            ->andWhere('cp.slot is null')
            ->setParameter('id', $event->getId())
            //  ->orderBy('c.lastname', 'ASC')
            //   ->orderBy('cp.createdAt')
            ->getQuery()
            ->getResult()
        ;


        return $qb;
    }

    /**
     *
     * @param Event $event
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEventQuery(Event $event, $filter = [], $orderby = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
        ;

        if (!empty($filter['search'])) {
            $qb->orwhere('c.email like :search')
                ->orWhere('c.firstname like :search')
                ->orWhere('c.lastname like :search')
                ->orWhere('c.phone like :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if(!empty($filter['rh']) && $filter['rh'] instanceof RhUser) {
            if($event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH) {
                $qb->leftJoin('App\Entity\RhUser', 'u')
                    ->leftJoin('u.recruitmentOffice', 'ro')
                    ->leftJoin('cp.status', 's2')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->orWhere('s2.slug = :slug2')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                    ->setParameter('slug2', 'registered')
                ;
            }
            else {
                $qb->join('App\Entity\RhUser', 'u')
                    ->join('u.recruitmentOffice', 'ro')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                ;
            }
        }
        else if(!empty($filter['ro'])){
            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'rh')
            ;
        }
        else if(!empty($filter['l4m'])){

            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'l4m')
            ;
        }
        else {
            if(!empty($filter['user']) && count($filter['user'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('u.id IN (:uid)')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('uid', $filter['user'])
                ;
            }
            if(!empty($filter['origin']) && count($filter['origin'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('c.origin = :uid')
                    ->setParameter('uid', $filter['origin'][0]->getId())
                ;
            }
        }
        if(!empty($filter['status'])) {
            if(is_object($filter['status']) && $filter['status'] instanceof ArrayCollection) {
                $statuses = array();
                foreach($filter['status'] as $status) {
                    $statuses[] = $status->getId();
                }
                $qb->andWhere('cp.status IN (:statuses)')
                    ->setParameter('statuses', $statuses);
            }
            else {
                $qb->join('cp.status', 's')
                    ->andWhere('cp.status = :status or s.slug =:status')
                    ->setParameter('status', $filter['status']);
            }
        }

        if(!empty($filter['mailing_recall'])) {
            $qb->andWhere('c.mailingRecall = true');
        }

        if(!empty($filter['rh_sectors'])) {
            if(is_object($filter['rh_sectors']) && $filter['rh_sectors'] instanceof ArrayCollection) {
                $sectors = array();
                foreach($filter['rh_sectors'] as $sector) {
                    $sectors[] = $sector->getId();
                }
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id IN (:sectors)')
                    ->setParameter('sectors', $sectors);
            }
            else {
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id = :sector')
                    ->setParameter('sector', $filter['rh_sectors']);
            }
        }

        if(array_key_exists('came',$filter) && $filter['came'] == true){
            $qb->andWhere('cp.scannedAt is not null');
        }

        if(!empty($filter['from'])) {

            $qb
                ->andWhere('cp.comesFrom = :from')
                ->setParameter('from', $filter['from']);

        }

        if(!empty($orderby) && count($orderby)) {
            foreach($orderby as $o) {
                switch($o) {
                    case 'name' :
                        $qb->orderBy('c.lastname')->addOrderBy('c.firstname');
                        break;
                    case 'created' :
                        $qb->orderBy('cp.createdAt');
                        break;
                }
            }
        }

        $qb
            ->andWhere('e.id = :id')
            ->setParameter('id', $event->getId())
            //  ->orderBy('c.lastname', 'ASC')
            //   ->orderBy('cp.createdAt')
            ->getQuery()
            ->getResult()
        ;


        return $qb;
    }

    public function findByEventAndScanned(Event $event, $filter = [])
    {
        return $this->findByEventAndScannedQuery($event, $filter)->getQuery()->getResult();
    }
    /**
     *
     * @param Event $event
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findWithOrigin(Event $event)
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->where('cp.event = :event')
            ->andWhere('c.origin is not null')
            ->andWhere('c.origin != 1')
            ->setParameter('event', $event->getId())
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }
    /**
     *
     * @param Event $event
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEventAndScannedQuery(Event $event, $filter = [], $orderby = [])
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
        ;

        if (!empty($filter['search'])) {
            $qb->orwhere('c.email like :search')
                ->orWhere('c.firstname like :search')
                ->orWhere('c.lastname like :search')
                ->orWhere('c.phone like :search')
                ->setParameter('search', '%'.$filter['search'].'%')
            ;
        }

        if(!empty($filter['rh']) && $filter['rh'] instanceof RhUser) {
            if($event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH) {
                $qb->leftJoin('App\Entity\RhUser', 'u')
                    ->leftJoin('u.recruitmentOffice', 'ro')
                    ->leftJoin('cp.status', 's2')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->orWhere('s2.slug = :slug2')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                    ->setParameter('slug2', 'registered')
                ;
            }
            else {
                $qb->join('App\Entity\RhUser', 'u')
                    ->join('u.recruitmentOffice', 'ro')
                    ->andWhere('ro.id = :roid')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
                ;
            }
        }
        else if(!empty($filter['ro'])){
            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'rh')
            ;
        }
        else if(!empty($filter['l4m'])){

            $qb->join('cp.handledBy', 'u')
                ->where('u instance of :discr')
                ->setParameter('discr', 'l4m')
            ;
        }
        else {
            if(!empty($filter['user']) && count($filter['user'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('u.id IN (:uid)')
                    ->andWhere('cp.handledBy = u')
                    ->setParameter('uid', $filter['user'])
                ;
            }
            if(!empty($filter['origin']) && count($filter['origin'])) {
                $qb->join('App\Entity\User', 'u')
                    ->andWhere('c.origin = :uid')
                    ->setParameter('uid', $filter['origin'][0]->getId())
                ;
            }
        }
        if(!empty($filter['status'])) {
            if(is_object($filter['status']) && $filter['status'] instanceof ArrayCollection) {
                $statuses = array();
                foreach($filter['status'] as $status) {
                    $statuses[] = $status->getId();
                }
                $qb->andWhere('cp.status IN (:statuses)')
                    ->setParameter('statuses', $statuses);
            }
            else {
                $qb->join('cp.status', 's')
                    ->andWhere('cp.status = :status or s.slug =:status')
                    ->setParameter('status', $filter['status']);
            }
        }

        if(!empty($filter['mailing_recall'])) {
            $qb->andWhere('c.mailingRecall = true');
        }

        if(!empty($filter['rh_sectors'])) {
            if(is_object($filter['rh_sectors']) && $filter['rh_sectors'] instanceof ArrayCollection) {
                $sectors = array();
                foreach($filter['rh_sectors'] as $sector) {
                    $sectors[] = $sector->getId();
                }
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id IN (:sectors)')
                    ->setParameter('sectors', $sectors);
            }
            else {
                $qb->join('c.rhSectors', 'rhs')
                    ->andWhere('rhs.id = :sector')
                    ->setParameter('sector', $filter['rh_sectors']);
            }
        }

        if(array_key_exists('came',$filter) && $filter['came'] == true){
            $qb->andWhere('cp.scannedAt is not null');
        }

        if(!empty($filter['from'])) {

            $qb
                ->andWhere('cp.comesFrom = :from')
                ->setParameter('from', $filter['from']);

        }

        if(!empty($orderby) && count($orderby)) {
            foreach($orderby as $o) {
                switch($o) {
                    case 'name' :
                        $qb->orderBy('c.lastname')->addOrderBy('c.firstname');
                        break;
                    case 'created' :
                        $qb->orderBy('cp.createdAt');
                        break;
                }
            }
        }

        $qb
            ->andWhere('e.id = :id')
            ->andWhere('cp.scannedAt is not null')
            ->setParameter('id', $event->getId())
            //  ->orderBy('c.lastname', 'ASC')
            //   ->orderBy('cp.createdAt')
            ->getQuery()
            ->getResult()
        ;


        return $qb;
    }

    /**
     *
     * @param Event $event
     * @param string $search
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEventAndInputQuery(Event $event, $search, $filter = [])
    {

        $qb = $this->createQueryBuilder('c')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
        ;

        if(!empty($filter['rh']) && $filter['rh'] instanceof RhUser) {
            $qb->join('cp.rh', 'rh')
                ->join('rh.recruitmentOffice', 'ro')
                ->andWhere('ro.id = :roid')
                ->setParameter('roid', $filter['rh']->getRecruitmentOffice()->getId())
            ;
        }

        if ($search){
            $qb->orwhere('c.email like :search')
                ->orWhere('c.firstname like :search')
                ->orWhere('c.lastname like :search')
            ;
        }
        $qb
            ->andWhere('e.id = :id')
            ->setParameters(array('id' => $event->getId(), 'search' => '%'.$search.'%'))
            ->orderBy('c.lastname', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        return $qb;
    }



    public function findByEventAndStatusQuery(Event $event, $slug, $filter = [], $orderby = [])
    {

        $filter['status'] = $slug;
        //  dump($filter);die();

        return $this->findByEventQuery($event, $filter, $orderby);
    }
    public function findByEventAndStatus(Event $event, $slug, $filter = [], $orderby = [])
    {
        return $this->findByEventAndStatusQuery($event,$slug,$filter,$orderby)->getQuery()->getResult();
    }


    public function findPhoneRecall(Event $event)
    {
        return $this->createQueryBuilder('c')
            ->select('c.phone')
            ->join('c.candidateParticipations', 'cp')
            ->join('cp.event', 'e')
            ->join('cp.status', 's')
            ->where('e.id = :id')
            ->andWhere('c.phoneRecall = true')
            ->andWhere('s.slug = :status')
            ->setParameters(array('id'=> $event->getId(), 'status' => 'confirmed'))
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return CandidateUser[] Returns an array of CandidateUser objects
//     */
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
    public function findOneBySomeField($value): ?CandidateUser
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
