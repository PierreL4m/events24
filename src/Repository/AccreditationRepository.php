<?php

namespace App\Repository;

use App\Entity\Accreditation;
use App\Entity\Event;
use App\Entity\Participation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EasyRdf\Literal\Integer;

/**
 * @method Accreditation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accreditation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accreditation[]    findAll()
 * @method Accreditation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccreditationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accreditation::class);
    }
    public function findByEvent(Event $event)
    {
        return $this->createQueryBuilder('a')
        ->join('a.event', 'e')
        ->where('e.id = :id')->setParameter('id', $event->getId())
        ->getQuery()
        ->getResult()
        ;
    }
    public function findById($id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
        }
    public function findByMail($mail, Event $event)
    {
        return $this->createQueryBuilder('a')
            ->where('a.email = :mail')->setParameter('mail', $mail)
            ->andWhere('a.event = :event')->setParameter('event', $event->getId())
            ->getQuery()
            ->getResult();
            ;
    }
    public function findDistinctByEvent(Event $event)
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT p.companyName, p.id')
            ->join('a.event', 'e')
            ->join('a.participation', 'p')
            ->where('e.id = :id')->setParameter('id', $event->getId())
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByEventAndParticipation(Event $event, $participation)
    {
        return $this->createQueryBuilder('a')
            ->join('a.event', 'e')
            ->where('e.id = :id')->setParameter('id', $event->getId())
            ->andWhere('a.participation = :participationId')->setParameter('participationId', $participation)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneByParticipation($participation)
    {
        return $this->createQueryBuilder('a')
            ->where('a.participation = :participationId')->setParameter('participationId', $participation)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Agenda[] Returns an array of Agenda objects
//     */

//    public function findByDescAgenda()
//    {
//        return $this->createQueryBuilder('a')
//            ->orderBy('a.id', 'DESC')
//            ->setMaxResults(1)
//            ->getQuery()
//            ->getResult()
//        ;
//    }



    public function findOneByIdDesc(): ?Accreditation
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

}
