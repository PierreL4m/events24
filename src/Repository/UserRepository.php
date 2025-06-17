<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Event;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UseRrea|null find($id, $lockMode = null, $lockVersion = null)
 * @method UseRrea|null findOneBy(array $criteria, array $orderBy = null)
 * @method UseRrea[]    findAll()
 * @method UseRrea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface, UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllQuery($type=null)
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.lastname', 'ASC');

        if ($type){
            $qb->where('u INSTANCE OF :discr')
                ->setParameter('discr', $type);
        }

        return $qb->getQuery() ;

    }

    public function loadUserByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        $qb = $this->createQueryBuilder('u');
        if(filter_var($usernameOrEmail, FILTER_VALIDATE_INT)) {
            $id = (int)$usernameOrEmail;
            $qb->where('u.id = :id')
                ->setParameter('id', $id);
        }
        return $qb
            ->orWhere('u.email = :email_or_username')
            ->orWhere('u.username = :email_or_username')
            ->setParameter('email_or_username', $usernameOrEmail)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByMail($mail)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email')->setParameter('email', $mail)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findMailCom()
    {
        $role = 'a:1:{i:0;s:10:"ROLE_ADMIN";}';
        $string = '%communication%';
        return $this->createQueryBuilder('u')
            ->select('u.email')
            ->where('u.roles = :role')->setParameter('role',$role)
            ->andwhere('u.position LIKE :string')->setParameter('string',$string)
            ->andwhere('u.enabled = 1')
            ->getQuery()
            ->getResult();

    }

    public function searchQuery($search)
    {
        $search_items = explode(" ", $search);
        $i = 0;
        $qb = $this->createQueryBuilder('u');

        foreach ($search_items as $search) {
            $qb->orWhere('u.firstname like :search'.$i)
                ->orWhere('u.lastname like :search'.$i)
                ->orWhere('u.email like :search'.$i)
                ->orWhere('u.username like :search'.$i)
                ->orWhere('u.phone like :search'.$i)
                ->setParameter('search'.$i, '%'.$search.'%');
            $i++;
        }


        // $qb->orWhere('u.firstname like :search')
        //      ->orWhere('u.lastname like :search')
        //      ->orWhere('u.email like :search')
        //      ->orWhere('u.username like :search')
        //      ->orWhere('u.phone like :search')
        //      ->setParameter('search', '%'.$search.'%')
        //      ->orderBy('u.lastname', 'ASC');


        return $qb->getQuery() ;

    }

    public function queryAllowedAdminsByEvent(Event $event, $strict = true) {
        if($strict) {
            $qb = $this->createQueryBuilder('u')
                ->join('App\Entity\CandidateParticipation', 'cp')
                ->where('cp.handledBy = u')
                ->andWhere('cp.event = :eid')
                ->setParameter('eid', $event->getId());
        }
        else {

            // FIXME : this is too ugly, what happens if we change roles ?!
            $qb = $this->createQueryBuilder('u');
            $qb ->leftJoin('App\Entity\RhUser', 'rh', Join::WITH, 'u.id = rh.id')
                ->leftJoin('rh.recruitmentOffice', 'ro')
                ->leftJoin('ro.events', 'ev',  Join::WITH, 'ev.id = :eid')
                ->where('ev.id IS NOT NULL')
                ->andWhere('u.roles LIKE :rolerh')
                ->orWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('u.roles', ':roleviewer'),
                        $qb->expr()->like('u.roles', ':roleadmin'),
                        $qb->expr()->like('u.roles', ':rolesuperadmin')
                    )
                )
                ->setParameter('eid', $event->getId())
                ->setParameter('rolerh', '%"ROLE_RH"%')
                ->setParameter('roleviewer', '%"ROLE_VIEWER"%')
                ->setParameter('roleadmin', '%"ROLE_ADMIN"%')
                ->setParameter('rolesuperadmin', '%"ROLE_SUPER_ADMIN"%')
            ;
            /*
            $qb = $this->createQueryBuilder('u');
            $qb ->leftJoin('App\Entity\RhUser', 'rh', Join::WITH, 'u.id = rh.id')
            ->leftJoin('rh.recruitmentOffice', 'ro')
            ->leftJoin('ro.events', 'ev')
//             ->where('ev.id IS NOT NULL')
             ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('u.roles', ':roleviewer'),
                    $qb->expr()->like('u.roles', ':roleadmin'),
                    $qb->expr()->like('u.roles', ':rolesuperadmin'),
                    $qb->expr()->andX('u.roles LIKE :rolerh', 'ev.id = :eid')
                )
             )
                ->setParameter('eid', $event->getId())
                ->setParameter('rolerh', '%"ROLE_RH"%')
                ->setParameter('roleviewer', '%"ROLE_VIEWER"%')
                ->setParameter('roleadmin', '%"ROLE_ADMIN"%')
                ->setParameter('rolesuperadmin', '%"ROLE_SUPER_ADMIN"%')
                ;
//             die($qb->getQuery()->getSQL());*/
        }
        return $qb;
    }

    /**
     * Returns Users who are allowed to validate CandidateParticipation for an Event
     * @param Event $event
     * @param boolean $strict set to true for users who have handled at least one participation, false to load full list of allowed users
     */
    public function findAllowedAdminsByEvent(Event $event, $strict = true) {
        return $this->queryAllowedAminsByEvent($event, $strict)->getQuery()->getResult();
    }

    public function countAppDwd($type, $device=null)
    {

        $qb = $this->createQueryBuilder('u')
            ->select('count(u.id)');

        if($device){
            $qb->andwhere('u.device = :device')
                ->setParameter('device',$device);
        }
        else{
            $qb->andwhere('u.device is not null');
        }

        if($type == 'client') {
            $qb->andWhere('u INSTANCE OF :discr or u INSTANCE OF :discr2')
                ->setParameter('discr', 'exposant')
                ->setParameter('discr2', 'exposant_scan');
        }
        else{
            $qb->andWhere('u INSTANCE OF :discr')
                ->setParameter('discr', $type);
        }





        return $qb->getQuery()->getSingleScalarResult() ;

    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('a')
            ->where('a.something = :value')->setParameter('value', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /**
     *
     * @param UserInterface $user
     */
    public function refreshUser(UserInterface $user) {
        $res = $this->loadUserByIdentifier($user->getUserIdentifier());
        return $res;
    }

    public function supportsClass($class) {
        return is_subclass_of(User::class, $class);
    }
}