<?php

namespace App\Repository;

use App\Entity\ClientUser;
use App\Entity\Event;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\Organization;
use App\Entity\Partner;
use App\Entity\RhUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\RecruitmentOffice;
use App\Entity\EventType;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


    public function findAllQuery()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.date', 'DESC');
            return $qb->getQuery()->getResult();
    }
    public function findByIdSectorPic($id)
    {
        return $this->createQueryBuilder('e')
        ->join('e.sectorPics', 'sp')
        ->where('sp.events = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
    }

    public function searchQuery($search)
    {
        $qb = $this->createQueryBuilder('e')
            ->join('e.place', 'p')
            ->join('e.manager', 'm')
            ->join('e.type', 't')
            ->where('p.city like :search')
            ->orWhere('e.date like :search')
            ->orWhere('m.firstname like :search')
            ->orWhere('t.fullName like :search')
            ->orWhere('t.shortName like :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('e.id', 'DESC');


        return $qb->getQuery()->getResult();

    }
    public function findCurrentEvents($max=null)
    {

        $qb = $this->createQueryBuilder('e')
            ->where('e.date >= :now')
            ->andWhere('0 = (SELECT COUNT(s) FROM App\Entity\Slots s WHERE s.event = e.id)')
            ->setParameter('now', date('Y-m-d'))
            ->orderBy('e.date', 'ASC')
        ;

        if ($max){
            $qb->setMaxResults($max);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findChildEvents(Event $event)
    {

        return $this->createQueryBuilder('e')
            ->where('e.parentEvent = :id')
            ->setParameter('id', $event->getId())
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    
    public function findCurrentEventsWithslots($max=null)
    {

        $qb = $this->createQueryBuilder('e')
            ->where('e.date >= :now')
            ->andWhere('0 < (SELECT COUNT(s) FROM App\Entity\Slots s WHERE s.event = e.id)')
            ->setParameter('now', date('Y-m-d'))
            ->orderBy('e.date', 'ASC')
        ;

        if ($max){
            $qb->setMaxResults($max);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findOneById(int $event)
    {
        return $this->createQueryBuilder('e')
            ->where('e.id = :event_id')
            ->setParameter('event_id', $event)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

     public function findHomePageEvents($host = null)
    {
        if(!empty($host)) {
            $qb = $this->createQueryBuilder('e')
                ->join('e.place', 'p')
                ->join('e.type', 't')
                ->join('t.hosts', 'h')
                ->andWhere('e.date >= :now')
                ->where('e.offline >= :now')
                ->andWhere('h.name = :host')
                ->setParameter('now', date('Y-m-d H:i:s'))
                ->setParameter('host', $host)
                ->orderBy('e.date', 'ASC')
                //->setMaxResults(6)
            ;
        }
        else {
            $qb = $this->createQueryBuilder('e')
                ->where('e.offline >= :now')
                ->setParameter('now', date('Y-m-d H:i:s'))
                ->orderBy('e.date', 'ASC')
                //->setMaxResults(6)
            ;
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findPastEvents($nb)
    {
        return $this->createQueryBuilder('e')
            ->where('e.date < :date')
            ->setParameter('date', date('Y-m-d'))
            ->orderBy('e.date', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getNextEventByCity($place_id)
    {
        return $this->createQueryBuilder('e')
            ->join('e.place', 'p')
            ->where('p.id = :id')
            ->andWhere('e.date >= :now')
            ->setParameters(array('id' => $place_id, 'now' => date('Y-m-d H:i:s') ))
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getNextEventByType(EventType $event_type)
    {

        return $this->createQueryBuilder('e')
            ->join('e.type', 'et')
            ->where('e.date >= :now')
            ->andWhere('et.id = :tid')
            ->setParameters(['now' => date('Y-m-d'), 'tid' => $event_type->getId()])
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByType(EventType $event_type)
    {
        return $this->createQueryBuilder('e')
            ->join('e.type', 'et')
            ->where('et.id = :id')
            ->setParameters(['id' => $event_type->getId()])
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findEventBySlug($slug)
    {
        return $this->createQueryBuilder('e')
            ->where('e.slug = :slug')
            ->setParameters(['slug' => $slug])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findNextExpert()
    {
        return $this->createQueryBuilder('e')
            ->where('e.type = 2')
            ->andWhere('e.date >= :now')
            ->setParameter('now', date('Y-m-d H:i:s'))
            ->orderBy('e.date','ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findRecall()
    {
        $qb = $this->createQueryBuilder('e');
       $query =
            $qb->where('e.date > :now')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('e.firstRecallDate', ':now'),
                    $qb->expr()->eq('e.secondRecallDate', ':now')
                )
            )
            ->setParameters(array('now' => date('Y-m-d') ))
            ->getQuery();
        return $query
            ->getResult()
        ;
    }
    public function findRecallArnaud($days)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string($days." days"));

        return $this->createQueryBuilder('e')
            ->where('e.date like :date')
            ->setParameters(array('date' => $date->format('Y-m-d%') ))
            ->getQuery()
            ->getResult()
        ;
    }

    /* if today is monday day = 1
     * return events if now+2days (wednesday) < event_date < now+5days (saturday)
     * else if today is wednesday day = 3
     *return events if now+4days (sunday) < event_date < now+8days (thursday)
     * else
     * return null (recall are sent only on monday and wedneday)
     */
    public function findD3RecallArnaud($today)
    {
        $day_number = date_format($today,'w'); // = day number of the week

        if($day_number == 1){
            $days_min = 2;
            $days_max = 5;
        }
        elseif ($day_number == 3) {
            $days_min = 4;
            $days_max = 8;
        }
        else{
            return [];
        }
        $date_min = clone($today);
        $date_max = clone($today);
        date_add($date_min,date_interval_create_from_date_string($days_min." days"));
        date_add($date_max,date_interval_create_from_date_string($days_max." days"));

        return $this->createQueryBuilder('e')
            ->where('e.date > :date_min')
            ->andWhere('e.date < :date_max')
            ->setParameters(array('date_min' => $date_min, 'date_max' => $date_max ))
            ->getQuery()
            ->getResult()
        ;
    }

    public function findfromDays($days)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string($days." days"));

        return $this->createQueryBuilder('e')
            ->where('e.date > :date')
            ->setParameters(array('date' => $date ))
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastByType($type)
    {

      return $this->createQueryBuilder('e')
          ->join('e.type', 'et')
          ->where('et.id = :id')
          ->andWhere('e.date <= :now')
          ->setParameter('now', date('Y-m-d'))
          ->setParameter('id', $type)
          ->orderBy('e.date','DESC')
          ->setMaxResults(1)
          ->getQuery()
          ->getResult()
          ;
    }

    public function findTodayEvent($max=null)
    {
        $date = new \DateTime(date('Y-m-d'));
        $qb = $this->createQueryBuilder('e')
            ->where('e.date like :now')
            ->setParameter('now', date('Y-m-d')."%")
            ->orderBy('e.date', 'ASC')
        ;

        if ($max){
            $qb->setMaxResults($max);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findLastSameCity(Event $event)
    {
        if(!$event->getPlace()){
            throw new \Exception('No place for event_id :'.$event->getId());
        }
        else{
            $place_id = $event->getPlace()->getId();
        }
        $event_date = $event->getDate() ? $event->getDate() : new \Datetime();

        return $this->createQueryBuilder('e')
            ->join('e.place', 'p')
            ->where('p.id = :id')
            ->andWhere('e.date < :event_date')
            ->setParameters(array('id'=> $place_id, 'event_date' => $event_date))
            ->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getMain48(Event $event)
    {
        $event_date = $event->getDate();
        $date_min = clone($event_date);
        $date_min->setTime(0,0);
        $date_max = clone($event_date);
        $date_max->setTime(23,59,59);

        date_add($date_min,date_interval_create_from_date_string("-1 days"));
        date_add($date_max,date_interval_create_from_date_string("1 days"));

        $qb = $this->createQueryBuilder('e')
        ->join('e.place','p')
        ->join('e.type', 'et')
        ->where('e.date >= :date_min and e.date <= :date_max')
        ->andWhere('p.id = :pid')
        ->andWhere('et.shortName = :st')
        ->setParameters(array(
            'date_min' => $date_min,
            'date_max' => $date_max,
            'pid' => $event->getPlace()->getId(),
            'st' => '48'
        ))
        ->orderBy('e.date', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ;

        return $qb->getOneOrNullResult();
    }

    public function getSecond48(Event $event)
    {
        $event_date = $event->getDate();

        $date_min = clone($event_date);
        $date_min->setTime(0,0);
        $date_max = clone($event_date);
        $date_max->setTime(23,59,59);

        date_add($date_min,date_interval_create_from_date_string("-1 days"));
        date_add($date_max,date_interval_create_from_date_string("1 days"));

        $qb = $this->createQueryBuilder('e')
            ->join('e.place','p')
            ->join('e.type', 'et')
            ->where('e.date >= :date_min and e.date <= :date_max')
            ->andWhere('e.id != :id')
            ->andWhere('p.id = :pid')
            ->andWhere('et.shortName = :st')
            ->setParameters(array(
                'date_min' => $date_min,
                'date_max' => $date_max,
                'id' => $event->getId(),
                'pid' => $event->getPlace()->getId(),
                'st' => '48'
            ))
            ->orderBy('e.date', 'DESC')
            ->getQuery()
        ;

        if (count($qb->getResult()) > 1){
            throw new \Exception('There is more than one second 48 event in getSecond48 query');
        }

        return $qb->getOneOrNullResult();
    }

     public function getOrderedByPartner(Partner $partner)
    {
        return $this->createQueryBuilder('e')
            ->join('e.partners','p')
            ->where('p.id = :id')
            ->setParameter('id', $partner->getId())
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastThreeMonthByOrganization(Organization $organization)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("30 days"));

        return $this->createQueryBuilder('e')
            ->join('e.participations', 'p')
            ->join('p.organization', 'o')
            ->where('e.date > :date')
            ->andwhere('e.date <= :date_now')
            ->andwhere('o.id = :id')
            ->setParameters(array('date' => $date, 'date_now' => new \DateTime, 'id' => $organization->getId() ))
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastThreeMonth()
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("90 days"));

        $qb = $this->createQueryBuilder('e');

        $qb->where('e.date > :date')
        ->setParameters(array('date' => $date));
        // $qb->where('u.id = :id')
        //     ->setParameters(array('id' => $user->getId() ));

        return $qb->getQuery()->getResult() ;
    }

    public function findLastThreeMonthByUser(ClientUser $user)
    {
        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("90 days"));

        $qb = $this->createQueryBuilder('e')
            ->join('e.participations', 'p');

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
        $qb->where('e.date > :date')
            ->andwhere('u.id = :id')
            ->setParameters(array('date' => $date, 'id' => $user->getId() ));
        // $qb->where('u.id = :id')
        //     ->setParameters(array('id' => $user->getId() ));

        return $qb->getQuery()->getResult() ;
    }

    public function findByDate(\Datetime $date)
    {
        $date = $date->format('Y-m-d');


        $qb = $this->createQueryBuilder('e')
            ->where('e.date like :date')
            ->setParameters(array('date' => $date.'%'))
            ->orderBy('e.date', 'DESC')
            ->getQuery()
        ;

        return $qb->getResult();
    }

    public function findTodayByClientUser(ClientUser $user)
    {
        $date_min = new \Datetime(date('Y-m-d')." 00:00:00");
        $date_max = new \Datetime(date('Y-m-d')." 23:59:59");

        $qb = $this->createQueryBuilder('e')
            ->join('e.participations', 'p');

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

        return $qb->getQuery()->getOneOrNullResult() ;
    }

    public function findByRhUserQuery(RhUser $user)
    {
        return $this->createQueryBuilder('e')
        ->join('e.recruitmentOffices', 'r')
        ->join('r.rhs', 'rh')
        ->where('rh.id = :id')
        ->setParameter('id', $user->getId())
        ->orderBy('e.date', 'DESC')
        ->getQuery()
        ;
    }
    public function findCurrentRecruitmentEvents(RecruitmentOffice $ro, $max=null)
    {

        $qb = $this->createQueryBuilder('e')
        ->join('e.recruitmentOffices', 'ro')
        ->where('e.date >= :now')
        ->andWhere('ro.id = :roid')
        ->setParameter('now', date('Y-m-d'))
        ->setParameter('roid', $ro->getId())
        ->orderBy('e.date', 'ASC')
        ;

        if ($max){
            $qb->setMaxResults($max);
        }

        return $qb->getQuery()
        ->getResult();
    }


    // public function findBySomething($value)
    // {
    //     return $this->createQueryBuilder('e')
    //         ->where('e.something = :value')->setParameter('value', $value)
    //         ->orderBy('e.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

}
