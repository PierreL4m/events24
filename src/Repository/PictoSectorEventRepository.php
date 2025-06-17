<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\SectorPic;
use App\Entity\PictoSectorEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictoSectorEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SectorPic::class);
    }


    public function findByEvent($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.event = :value')->setParameter('value', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEvents(Event $event, Event $second_event)
    {

        return $this->createQueryBuilder('s')
            //->addSelect('st.id')
            ->join('s.event', 'e')
            ->join('s.event', 'e2')
            ->join('s.sectionType','st')
            ->where('e.id = :value')->setParameter('value', $event->getId())
            ->orwhere('e2.id = :id2')->setParameter('id2', $second_event->getId())
            //should be ->orderBy('s.sOrder', 'ASC')
            ->addOrderBy('e.date', 'ASC')
            //->distinct('st.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findConceptByEvent(Event $event)
    {
        return $this->createQueryBuilder('s')
            //->addSelect('st.id')
            ->join('s.event', 'e')
            ->join('s.sectionType','st')
            ->where('e.id = :value')->setParameter('value', $event->getId())
            ->andwhere('st.slug = :concept')
            ->setParameter('concept','concept')
            //should be ->orderBy('s.sOrder', 'ASC')
            ->addOrderBy('e.date', 'ASC')
            //->distinct('st.id')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAgendaByEvents(Event $event, Event $second_event)
    {

        return $this->createQueryBuilder('s')
            //->addSelect('st.id')
            ->join('s.event', 'e')
            ->join('s.event', 'e2')
            ->join('s.sectionType','st')
            ->where('e.id = :value')->setParameter('value', $event->getId())
            ->orwhere('e2.id = :id2')->setParameter('id2', $second_event->getId())
            ->andwhere('st.slug = :agenda')
            //should be ->orderBy('s.sOrder', 'ASC')
            ->setParameter('agenda','agenda')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByEventForBilan(Event $event)
    {
        return $this->createQueryBuilder('s')
            ->join('s.event', 'e')
            ->join('s.sectionType','st')
            ->where('e.id = :value')
            ->andWhere('s.onBilan = true')
            ->setParameters(array('value' => $event->getId()))
            //should be ->orderBy('s.sOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStSlugAndEvent(Event $event, $slug)
    {
        return $this->createQueryBuilder('s')
            ->join('s.event','e')
            ->join('s.sectionType', 'st')
            ->where('e.id = :id')
            ->andWhere('st.slug = :slug')
            ->setParameters(array('id' => $event->getId(), 'slug' => $slug))
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
