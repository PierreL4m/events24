<?php
namespace App\Tests\Behat;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\ExposantScanUser;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\ParticipationDefault;
use App\Entity\User;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\EventType;
use App\Entity\EventJobs;
use App\Entity\EventSimple;
use App\Entity\Job;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Entity\AccessToken;
use League\Bundle\OAuth2ServerBundle\Manager\Doctrine\ClientManager;
use App\Entity\ResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;

/**
 * Defines application features from the specific context.
 */
class DoctrineContext  implements Context
{
    
    /**
     * 
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    
    /**
     * 
     * @var SessionInterface
     */
    private SessionInterface $session;
    
    /**
     * 
     * @var UserPasswordHasher
     */
    private UserPasswordHasher $passwordHasher;
    
    /**
     *
     * @var AccessTokenManagerInterface
     */
    private AccessTokenManagerInterface $tokenManager;
    
    /**
     * 
     * @var ClientManager
     */
    private ClientManager $clientManager;
    
    public function __construct(
        EntityManagerInterface $em, 
        SessionInterface $session, 
        UserPasswordHasherInterface $passwordHasher, 
        AccessTokenManagerInterface $tokenManager,
        ClientManager $clientManager) {
        $this->em = $em;
        $this->session = $session;
        $this->passwordHasher = $passwordHasher;
        $this->tokenManager = $tokenManager;
        $this->clientManager = $clientManager;
    }
    
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    public function getEventTypes() {
        return $this->getEm()->getRepository(EventType::class)->findAll();
    }

    public function getEventSimple(): EventSimple {

        $qb = $this->getEm()->getRepository(EventSimple::class)->createQueryBuilder('e');
        return $qb->join('e.type', 'et')
        ->join('e.participations', 'p')
        ->where('et.registrationType = :t')
        ->setParameter('t', EventType::REGISTRATION_TYPE_STANDARD)
        ->orderBy('e.date', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult()
        [0];

    }

    public function getEventJobs(): EventJobs {
        $qb = $this->getEm()->getRepository(EventJobs::class)->createQueryBuilder('e');
        return $qb->join('e.type', 'et')
        ->join('e.participations', 'p')
        ->where('et.registrationType != :t')
        ->andWHere('e.registrationLimit IS NULL OR e.registrationLimit > :d')
        ->setParameter('t', EventType::REGISTRATION_TYPE_JOB)
        ->setParameter('d', new \Datetime())
        ->orderBy('e.date', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult()
        [0];
    }

    public function getEventJobsWithRegistrationTypeJob(): EventJobs
    {
        $qb = $this->getEm()->getRepository(EventJobs::class)->createQueryBuilder('e');

        $sub = $this->getEm()->getRepository(Job::class)->createQueryBuilder('j');
        $sub->where('j.participation = p.id');

        return $qb->join('e.type', 'et')
            ->join('e.participations', 'p')
            ->where('et.registrationType = :t')
            ->andWHere('e.registrationLimit is null OR e.registrationLimit > :d')
            ->andWHere(
                $qb->expr()->exists($sub->getDQL())
            )
            ->setParameter('t', EventType::REGISTRATION_TYPE_JOB)
            ->setParameter('d', new \Datetime())
            ->orderBy('e.date', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            [0];
    }

    public function getEventRandomJob(Event $event): Job
    {
        $qb = $this->getEm()->getRepository(Job::class)->createQueryBuilder('j');
        return $qb->join('j.participation', 'p')
            ->join('p.event', 'e')
            ->where('e.id = :id')
            ->setParameter('id', $event->getId())
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            [0];
    }

    public function getEventJobsWithoutAutoValidationAndWithoutJobRegistration(): EventJobs {
        $qb = $this->getEm()->getRepository(EventJobs::class)->createQueryBuilder('e');
        return $qb->join('e.type', 'et')
        ->join('e.participations', 'p')
        ->where('et.registrationType != :t')
        ->andWhere('et.registrationValidation != :v')
        ->andWHere(
            $qb->expr()->orX(
                $qb->expr()->isNull('e.registrationLimit'),
                $qb->expr()->gt('e.registrationLimit', ':d')
            )
        )
        ->setParameter('t', EventType::REGISTRATION_TYPE_JOB)
        ->setParameter('v', EventType::REGISTRATION_VALIDATION_AUTO)
        ->setParameter('d', new \Datetime())
        ->orderBy('e.date', 'ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult()
        [0];
    }

    /**
     * @BeforeScenario @resetPasswordRequest
     */
    public function resetPasswordRequest()
    {
        $em = $this->getEm();
        $user = $em->getRepository(User::class)->findOneByEmail('france.benoit@l4m.fr');
        $user->setPasswordRequestedAt(null);
        $em->flush();
    }

    /**
     * @BeforeScenario @deleteCandidate
     */
    public function deleteCandidate()
    {
        $em = $this->getEm();
        $user = $em->getRepository(User::class)->findOneByEmail('testtest@l4m.fr');
        if($user){
            $em->remove($user);
        }
        $em->flush();
    }
    /**
     * @BeforeScenario @deleteCandidateTest
     */
    public function deleteCandidateTest()
    {
        $em = $this->getEm();
        $user = $em->getRepository(User::class)->findOneByEmail('testtestedit@l4m.fr');
        if($user){
            $em->remove($user);
        }
        $em->flush();
    }
    /**
     * @BeforeScenario @loadEventIdInSession
     */
    public function loadEventIdInSession()
    {
        // to do fix this
        // only check if event exists
        // set to session to avoid change here and not in ApiContext
        $em = $this->getEm();
        $id = 10000;
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event){
            throw new \Exception('event_id '.$id.' not found');
        }
        $this->session->set('event_id', $event->getId());
    }

    public function getCandidateParticipationInFixtures()
    {
        $em = $this->getEm();
        $candidate = $em->getRepository(CandidateUser::class)->findOneByEmail('candidate@l4m.fr');
        $event = $em->getRepository(Event::class)->find(10000);
        $candidate_participation = $em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($candidate,$event);

        return $candidate_participation;
    }
    /**
     * @BeforeScenario @loadCandidateParticipationIdInSessionAndDeleteComment
     */
    public function loadCandidateParticipationIdInSessionAndDeleteComment()
    {
        $candidate_participation = $this->getCandidateParticipationInFixtures() ;
        $this->session->set('candidate_participation_id', $candidate_participation->getId());

        $comments = $candidate_participation->getCandidateComments();
        if(count($comments) > 0 && !is_null($comments[0])) {
            $candidate_participation->removeCandidateComment($comments[0]);
        }
        return $candidate_participation;
    }

     /**
     * @Given I create a new organization
     */
    public function iCreateANewOrganization()
    {
        $em = $this->getEm();
        
        //remove user and organization before create it
        $user = $em->getRepository(User::class)->findOneByUsername('test_organization');

        if($user){
            $em->remove($user);
            $em->flush();
        }
        $organization = $em->getRepository(Organization::class)->findOneByName('test_organization');
        
        if($organization){
            foreach ($organization->getParticipations() as $p) {

                $p->setOrganization(null);
                //to do fix this should be $em->remove($p); thow error
            }
            $em->remove($organization);
        }
        $em->flush();
        //end remove

        //create
        $organization = new Organization();
        $organization->setName('test_organization');
        $organization->setInternalName('test_organization');
        
        $em->persist($organization);
        try {
            $em->flush();
        }
        catch(\Exception $e) {
            die($e->getTraceAsString());
        }
        
        if ($organization->getName() != 'test_organization'){
            throw new \Exception('Organization name not equal to test_organization');
        }
        $this->session->set('organization', $organization);
        
    }

    /**
     * @Then I should have a new ExposantScanUser and I give it a bearer
     */
    public function iShouldHaveANewExposantscanuserAndIGiveItABearer()
    {
        $em = $this->getEm();
        $organization = $this->session->get('organization') ;

        /**
         * 
         * @var ExposantScanUser $exposant_user
         */
        $exposant_user = $em->getRepository(ExposantScanUser::class)->findOneByOrganization($organization->getId());

        if($organization->getName() != $exposant_user->getUsername()){
            throw new \Exception('The organization name is not equal to the exposant scan user username');
        }
        
        $password = 'abcd1234!';
        $hashedPassword = $this->passwordHasher->hashPassword(
            $exposant_user,
            $password
        );
        $exposant_user->setPassword($hashedPassword);
        $exposant_user->setSavedPlainPassword($password);
        $this->em->persist($exposant_user);  
        $em->flush();
        
        $this->session->set('exposant_scan_user',$exposant_user) ;
        
        $client = $this->clientManager->find('465fd597a66ca51b3cae150ed712db2b');
        $token_name = '517b8f9eac0a7201bc76dfb0cefe2ed846bca87bfa4086edf8a304486c03ce30cbbbcf6751ab54ed';
        if(!($token = $this->tokenManager->find($token_name)))
            $token = new AccessToken(
                $token_name,
                new \DateTimeImmutable(date('Y-m-d H:i:s', 1756364913)),
                $client,
                $client->getIdentifier(),
                $exposant_user->getRoles()
            );
            $this->tokenManager->save($token);
            $em->flush();  
/* OLD

        //set test token
        $token_name = 'test_organization';
        $token = $em->getRepository(AccessToken::class)->findOneByToken($token_name);

        if (!$token){
            $token = new AccessToken();
            $token->setClient($em->getRepository(Client::class)->find(3));
            $token->setToken($token_name);
            $token->setExpiresAt(1756364913);
            $token->setUser($exposant_user);
            $em->persist($token);
        }
        $em->flush();*/
    }



    /**
     * @Given I add the organization to the event in fixtures
     */
    public function iAddTheOrganizationToTheEventInFixtures()
    {
        $em = $this->getEm();
        $organization = $em->getRepository(Organization::class)->findOneByName('test_organization');
        $event = $em->getRepository(Event::class)->find(10000);

        $participations = $em->getRepository(Participation::class)->findByOrganizationAndEvent($organization,$event);

        if(count($participations) == 0){
            $participation = new ParticipationDefault();
            $participation->setEvent($event);
            $participation->setOrganization($organization);
            $participation->setCompanyName('Participation Test Organization for ExposantScanUser');
            $em->persist($participation);
            $em->flush();
        }

    }
    
    /**
     * @Given user with email :email has no active password request
     */
    public function userHasNoActivePasswordRequest($email) {
        $em = $this->getEm();
        /**
         * 
         * @var User[] $users
         */
        $users = $em->getRepository(User::class)->findByEmail($email);
        
        /**
         * @var ResetPasswordRequestRepository $requestRepo
         */
        $requestRepo = $em->getRepository(ResetPasswordRequest::class);
        
        foreach($users as $user) {
            echo $user->getId()."\n";
            $user->setPasswordRequestedAt(null);
            $user->setConfirmationToken(null);
            $em->persist($user);
            
            $requests = $requestRepo->findByUser($user);
            foreach($requests as $r) {
                $requestRepo->removeResetPasswordRequest($r);
            }
        }
        
        $em->flush();
    }
}
