<?php

namespace App\DataFixtures;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\City;
use App\Entity\Color;
use App\Entity\ClientUser;
use App\Entity\Degree;
use App\Entity\Event;
use App\Entity\EventJobs;
use App\Entity\EventSimple;
use App\Entity\EventType;
use App\Entity\ExposantUser;
use App\Entity\Host;
use App\Entity\Job;
use App\Entity\Joblink;
use App\Entity\JoblinkSession;
use App\Entity\L4MUser;
use App\Entity\Mobility;
use App\Entity\Organization;
use App\Entity\OrganizationType;
use App\Entity\Participation;
use App\Entity\ParticipationJobs;
use App\Entity\ParticipationDefault;
use App\Entity\Place;
use App\Entity\ScanUser;
use App\Entity\Section;
use App\Entity\SectionSector;
use App\Entity\SectionSimple;
use App\Entity\SectionType;
use App\Entity\Sector;
use App\Entity\SpecBase;
use App\Entity\Status;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use App\Helper\ParticipationHelper;
use App\Factory\ParticipationFactory;
use App\Entity\EventTypeParticipationType;
use App\Entity\ContractType;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken;
use League\Bundle\OAuth2ServerBundle\Model\Grant;
use League\Bundle\OAuth2ServerBundle\Model\Scope;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use App\Entity\Slots;
use App\Entity\ExposantScanUser;

class AppFixtures extends Fixture  implements FixtureGroupInterface
{
    private $events_id = [10000,20000,30000,40000];//[20000,30000];

    /**
     *
     * @var ParticipationFactory
     */
    private $participation_factory;

    /**
     *
     * @var ClientManagerInterface
     */
    private $client_manager;

    /**
     *
     * @var AccessTokenManagerInterface
     */
    private $token_manager;

    public function __construct(ParticipationFactory $participation_factory, ClientManagerInterface $client_manager, AccessTokenManagerInterface $token_manager) {
        $this->participation_factory = $participation_factory;
        $this->client_manager = $client_manager;
        $this->token_manager = $token_manager;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadClientTokenAndUser($manager);
        $this->createHosts($manager);
        $this->createEventTypes($manager);
        $this->createPlaces($manager);
        $event = $this->createEventSimpleAndAddExposant($manager);
        $this->registerCandidateToEvent($manager,$event);
        $event = $this->createEventExpertsReims($manager);
        $this->registerCandidateToEvent($manager,$event);
        $event = $this->createEventExpertsLille($manager);
        $this->registerCandidateToEvent($manager,$event);
        $event = $this->createEventEscapeLille($manager);
        $this->createEventWithSlots($manager);
        $candidateparticipation = $this->registerCandidateToEvent($manager,$event);
        $this->addJobJoblinkAndJoblinkSession($manager, $event, $candidateparticipation);
        $this->addExposant($manager);
        $this->loadCandidates($manager); //load candidates for $this->events_id
    }

    public function loadClientTokenAndUser(EntityManagerInterface $manager)
    {
        $client = $this->client_manager->find('465fd597a66ca51b3cae150ed712db2b');

        if (!$client){
            $client = (new Client('usekey', '465fd597a66ca51b3cae150ed712db2b', '561b5d487befb18261992b47887f1e1b122ceb50f0646ae3f7d2f8b5a5e75787d72e2ffd1a931bd428665c339828cae99249dfe7e1cc62108451246af87273d0'))
                ->setGrants(...array_map(static function (string $grant): Grant {
                    return new Grant($grant);
                }, array ('client_credentials', 'password','refresh_token')))
                ->setScopes(...array_map(static function (string $scope): Scope {
                    return new Scope($scope);
                }, array ('SUPER_USER')))
            ;

            $this->client_manager->save($client);
            // $client = $this->client_manager->find('4q3qcd3g1rmsw²0s0ckcs4wk0wksg4gwk40sgcwscso00coo8wc');
        }
        $token_name = '75c0980ca7e0bcc318f5daa3bd1d25e4bc11577094806671557e0e7a7e882ec43f44b45f99e9e4a5';
        if(!($token = $this->token_manager->find($token_name)))
        $token = new AccessToken(
            $token_name,
            new \DateTimeImmutable(date('Y-m-d H:i:s', 1756364913)),
            $client,
            $client->getIdentifier(),
            ['SUPER_USER']
        );
        $this->token_manager->save($token);
        $manager->flush();

        $users = array(
            'candidate' => array(
                'class' => CandidateUser::class,  
                'identifier' => 'dsdsdfsdfsdfsdfsdf'
            ),
            'scan' => array(
                'class' => ScanUser::class, 
                'identifier' => '8594bc533994c592d218cd47b1a64d4aa0ed0ceac976360706abcda6d8b08d312b55fb9b52e3ae07'
            ),
            'exposant' => array(
                'class' => ExposantUser::class, 
                'identifier' => '9e1c7f9a77d76a96dc4368dc5de2020e0721ed8274e0ae798f48783070a9c69cfe8de63183e01f3e'
            ),
            'exposantscan' => array(
                'class' => ExposantScanUser::class,
                'identifier' => '517b8f9eac0a7201bc76dfb0cefe2ed846bca87bfa4086edf8a304486c03ce30cbbbcf6751ab54ed'
            ),
            'l4mtest' => array(
                'class' => L4MUser::class,
                'identifier' => 'd22bfc9d9912cfba8f500093954a17b37200dfef6d404ba1dc76a226b7ab8673f9cb1a0de975e420'
            )
        );

        foreach ($users as $name => $tab) {

            $managerail = $name.'@l4m.fr' ;
            $user = $manager->getRepository(User::class)->findOneByEmail($managerail) ;

            if (!$user ){
                $class = $tab['class'];
                $user = new $class;
                $user->setFirstname($name);
                $user->setUsername($name);
                $user->setLastname($name);
                $user->setEmail($managerail);
                $user->setSalt('');
                $user->setPhone('0320202020');
                $user->setPlainPassword($name);
                $user->setEnabled(true);

                switch (get_class($user)) {
                    case CandidateUser::class :
                        $user->setRoles(array('ROLE_CANDIDATE'));
                        $user->setMailingEvents(true);
                        $user->setWantedJob("webmaster");
                        $mobility = $manager->getRepository(Mobility::class)->find(1);
                        $user->setMobility($mobility);
                        $degree = $manager->getRepository(Degree::class)->find(1);
                        $user->setDegree($degree);
                        $user->setCv('docx.docx');
                        $city = $manager->getRepository(City::class)->find(1);
                        $user->setCity($city);

                        break;
                    case ScanUser::class :
                        $user->addRole('ROLE_SCAN');
                        break;
                    case ExposantUser::class :
                        $user->addRole('ROLE_ORGANIZATION');
                        break;
                    case ExposantScanUser::class :
                        $user->addRole('ROLE_EXPOSANT_SCAN');
                        break;
                    case L4MUser::class :
                        $user->addRole('ROLE_ADMIN');
                        break;
                    default:
                        // code...
                        break;
                }
                $manager->persist($user);
            }
            $token_name = $tab['identifier'];
            $token = $this->token_manager->find($token_name);

            if (!$token){
                $token = new AccessToken(
                    $token_name,
                    new \DateTimeImmutable(date('Y-m-d H:i:s', 1756364913)),
                    $client,
                    $user->getEmail(),
                    $user->getRoles()
                );
                $this->token_manager->save($token);
            }
            $manager->flush();
        }
    }

    public function createEventTypes($manager) {
        $event_type = $manager->getRepository(EventType::class)->findOneByShortName('Experts');
        if(empty($event_type)) {
            $event_type = new EventType();
            $event_type->setShortName('Experts');
            $event_type->setFullName('Recrutement Experts');
            $event_type->setHost($manager->getRepository(Host::class)->findOneByName('www.recrutementexperts.fr'));
            $event_type->addHost($manager->getRepository(Host::class)->findOneByName('www.recrutementexperts.fr'));
        }
        $event_type->setAnalyticsId('');
        $event_type->setRegistrationValidation(EventType::REGISTRATION_VALIDATION_VIEWER);
        $event_type->setRegistrationType(EventType::REGISTRATION_TYPE_STANDARD);
        $event_type->setRegistrationJoblinks(EventType::REGISTRATION_DONT_USE_JOBLINKS);
        $event_type->setDisplayParticipationContactInfo(false);
        $manager->persist($event_type);

        $manager->flush();

        $event_type = $manager->getRepository(EventType::class)->findOneByShortName('Escape');
        if(empty($event_type)) {
            $event_type = new EventType();
            $event_type->setShortName('Escape');
            $event_type->setFullName('Escape Game Recrut');
            $event_type->setHost($manager->getRepository(Host::class)->findOneByName('www.escapegamerecrut.fr'));
            $event_type->addHost($manager->getRepository(Host::class)->findOneByName('www.escapegamerecrut.fr'));
        }
        $event_type->setAnalyticsId('');
        $event_type->setRegistrationType(EventType::REGISTRATION_TYPE_JOB);
        $event_type->setRegistrationValidation(EventType::REGISTRATION_VALIDATION_VIEWER_RH);
        $event_type->setRegistrationJoblinks(EventType::REGISTRATION_USE_JOBLINKS);
        $event_type->setDisplayParticipationContactInfo(false);
        $manager->persist($event_type);


        $participation_types = $event_type->getParticipationTypes();
        if(count($participation_types)) {
            foreach($participation_types as $pt) {
                $manager->remove($pt);
            }
            $manager->flush();
        }

        // EventTypeParticipationType
        $organization_types = $manager->getRepository(OrganizationType::class)->findAll();
        foreach($organization_types as $organization_type) {
            $e = new EventTypeParticipationType();
            $e->setOrganizationType($organization_type);
            $e->setParticipationClass('ParticipationJobs');
            $e->setEventType($event_type);
            $manager->persist($e);
        }

        $manager->flush();

    }

    public function createHosts($manager) {
        $host = $manager->getRepository(Host::class)->findOneByName('www.escapegamerecrut.fr');
        if(empty($host)) {
            $host = new Host();
            $host ->setName('www.escapegamerecrut.fr');
        }
        $manager->persist($host);
        $manager->flush();
    }


    public function createPlaces($manager) {
        // Salon Degermann (Reims)
        $place = $manager->getRepository(Place::class)->findBySlug('reims'); //Salon Degermann (Reims)
        if(empty($place)) {
            $place = new Place();
            $place
                ->setActive(true)
                ->setName('Salon Degermann')
                ->setAddress('35 Rue Buirette')
                ->setCp('51100')
                ->setCity('Reims')
                ->setSlug('reims')
                ->setLatitude('49.254052')
                ->setLongitude('4.024749')
            ;

            $place->addColor(
                (new Color())
                    ->setCode('#6cb7b2')
                    ->setName('color_1')
            );

            $manager->persist($place);
            $manager->flush();
        }

        // CCI Grand lille (18)
        $place = $manager->getRepository(Place::class)->findBySlug('lille'); //cci grand lille
        if(empty($place)) {
            $place = new Place();
            $place
                ->setActive(true)
                ->setName('CCI Grand Lille')
                ->setAddress('Place du Theatre')
                ->setCp('59000')
                ->setCity('Lille')
                ->setSlug('lille')
                ->setLatitude('47.44778')
                ->setLongitude('6.58611')
            ;

            $place->addColor(
                (new Color())
                    ->setCode('#6cb7b2')
                    ->setName('color_1')
            );
            $manager->persist($place);
            $manager->flush();
        }

        // Get Out
        $place = $manager->getRepository(Place::class)->findOneBySlug('lille_getout'); //get out lille
        if(empty($place)) {
            $place = new Place();
            $place
                ->setActive(true)
                ->setName('Get Out')
                ->setAddress('128 Rue Solférino')
                ->setCp('59000')
                ->setCity('Lille')
                ->setSlug('lille_getout')
                ->setLatitude('50.631988')
                ->setLongitude('3.053148')
            ;

            $place->addColor(
                (new Color())
                    ->setCode('#6cb7b2')
                    ->setName('color_1')
            );

            $manager->persist($place);
            $manager->flush();
        }
    }

    // evenement en cours avec slots    
    public function createEventWithSlots($manager)
    {
        $event = $manager->getRepository(EventSimple::class)->findOneById(111111);
        if(!$event) {
            $event = new EventSimple();
            $event_type = $manager->getRepository(EventType::class)->findOneByShortName('24');
            $event->setType($event_type);
            $place = $manager->getRepository(Place::class)->find(2);
            $event->setPlace($place);

            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($manager->getRepository(User::class)->find(2));
            $event->setDate(new \Datetime);
            $event->setOnline(new \Datetime);
            $event->setOffline(new \Datetime);
            $event->setClosingAt(new \Datetime);
            $event->setNbStand(20);
            $specBase = $manager->getRepository(SpecBase::class)->findOneBy(['name' => 'Cahier des charges par défaut']);
            $event->setSpecBase($specBase);

            $event->setId(111111); // this is very important for behat test
            $metadata = $manager->getClassMetaData(EventSimple::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }

        if(!($s = $event->getSections()) || !$s->count()){
            $this->addRegistrationToEvent($manager,$event);
        }

        $event->setSlug('event-simple-slots-test');
        $event->setDate(new \Datetime);
        $event->sethas_slots(1);
        $manager->persist($event);

        // creer au moins 2 slots
        $slot = new Slots();
        $slot->setEvent($event);
        $slot->setName('9h30 - 10h30');
        $slot->setis_full(false);
        $slot->setMaxCandidats(100);
        $slot->setBeginSlot(new \DateTime(date('Y-m-d').' 09:30:00'));
        $slot->setEndingSlot(new \DateTime(date('Y-m-d').' 10:30:00'));
        $manager->persist($slot);

        $slot = new Slots();
        $slot->setEvent($event);
        $slot->setName('10h30 - 11h30');
        $slot->setis_full(false);
        $slot->setMaxCandidats(100);
        $slot->setBeginSlot(new \DateTime(date('Y-m-d').' 10:30:00'));
        $slot->setEndingSlot(new \DateTime(date('Y-m-d').' 11:30:00'));
        $manager->persist($slot);

        $manager->flush();
    }

    //evenement simple
    public function createEventSimpleAndAddExposant($manager)
    {
        $event = $manager->getRepository(EventSimple::class)->findOneById(10000);

        if(!$event){
            $event = new EventSimple();
            $event_type = $manager->getRepository(EventType::class)->findOneByShortName('24');
            $event->setType($event_type);
            $place = $manager->getRepository(Place::class)->find(2);
            $event->setPlace($place);

            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($manager->getRepository(User::class)->find(2));
            $specBase = $manager->getRepository(SpecBase::class)->findOneBy(['name' => 'Cahier des charges par défaut']);
            $event->setSpecBase($specBase);

            $event->setId(10000); // this is very important for behat test
            $metadata = $manager->getClassMetaData(EventSimple::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }
        if(!($s = $event->getSections()) || !$s->count()){
            $this->addRegistrationToEvent($manager,$event);
            $this->addContactToEvent($manager, $event);
        }

        $event->setSlug('event-simple-test');
        $event->setDate(new \Datetime);
        $event->setOnline(new \Datetime);
        $event->setOffline(new \Datetime);
        $event->setClosingAt(new \Datetime);
        $event->setNbStand(20);

        $manager->persist($event);
        $manager->flush();

        return $event ;
    }

    // experts reims
    public function createEventExpertsReims($manager)
    {
        $event = $manager->getRepository(EventJobs::class)->findOneBySlug('experts-reims-test');
        $event_by_id = $manager->getRepository(EventJobs::class)->find(30000);
        if(!$event && !$event_by_id){
            $event = new EventJobs();
            $event_type = $manager->getRepository(EventType::class)->findOneByShortName('Experts');
            $event->setType($event_type);
            $place = $manager->getRepository(Place::class)->findOneBy(['slug' =>'reims']); //salon degermann reims
            $event->setPlace($place);

            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($manager->getRepository(User::class)->find(2));
            $event->setSlug('experts-reims-test');
            $event->setDate(new \Datetime);
            $event->setOnline(new \Datetime);
            $event->setOffline(new \Datetime);
            $event->setClosingAt(new \Datetime);
            $event->setNbStand(20);
            $specBase = $manager->getRepository(SpecBase::class)->findOneBy(['name' => 'Cahier des charges par défaut']);
            $event->setSpecBase($specBase);

            $event->setId(30000); // this is very important for behat test
            $metadata = $manager->getClassMetaData(EventJobs::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }
        else {
            $event->setDate(new \Datetime);
            $event->setOnline(new \Datetime);
            $event->setOffline(new \Datetime);
            $event->setClosingAt(new \Datetime);
            $event->setRegistrationLimit(new \DateTime(date('Y-m-d H:i:s', time()+86400*2)));
        }
        if(!$event && $event_by_id) {
            $event = $event_by_id;
            $event->setSlug('experts-reims-test');
            $event->setDate(new \Datetime);
        }

        //sections
        if(!($s = $event->getSections()) || $s->count() == 0){
            $this->addRegistrationToEvent($manager,$event);

            //sectors
            $section_type = $manager->getRepository(SectionType::class)->findOneBySlug('sectors');
            $section_sectors = new SectionSector();
            $section_sectors->setSectionType($section_type);
            $section_sectors->setTitle($section_type->getTitle());
            $section_sectors->setMenuTitle($section_type->getTitle());
            // OLD $section_sectors->setSorder($section_type->getSorder());
            $section_sectors->setSorder(2);
            $section_sectors->setOnPublic(true);
            $section_sectors->setEvent($event);
            $manager->persist($section_sectors);
        }

        $sector = $manager->getRepository(Sector::class)->find(1); //Administratif / gestion
        $sector2 = $manager->getRepository(Sector::class)->find(5); //Banque / finance


        //to do check if sector already in event
        if($event->getSectors()){
            foreach($event->getSectors() as $sectorEvent) {
                $event->removeSector($sectorEvent);
            }
        }
        $event->addSector($sector);
        $event->addSector($sector2);

        //partners

        // $this->addExposant($manager,$event);

        $manager->persist($event);
        $manager->flush();

        return $event;
    }

    // experts reims
    public function createEventEscapeLille($manager)
    {
        $event = $manager->getRepository(EventJobs::class)->findOneBySlug('escape-lille-test');
        $event_by_id = $manager->getRepository(EventJobs::class)->find(40000);
        if(!$event && !$event_by_id){
            $event_type = $manager->getRepository(EventType::class)->findOneByShortName('Escape');
            $event = new EventJobs();
            $event->setType($event_type);
            $place = $manager->getRepository(Place::class)->findOneBySlug('lille_getout'); // get out lille
            $event->setPlace($place);

            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($manager->getRepository(User::class)->find(2));
            $event->setSlug('escape-lille-test');
            $event->setDate(new \Datetime);
            $event->setOnline(new \Datetime);
            $event->setOffline(new \Datetime);
            $event->setClosingAt(new \Datetime);
            $event->setNbStand(20);
            $specBase = $manager->getRepository(SpecBase::class)->findOneBy(['name' => 'Cahier des charges par défaut']);
            $event->setSpecBase($specBase);

            $event->setId(40000); // this is very important for behat test
            $metadata = $manager->getClassMetaData(EventJobs::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }
        if(!$event && $event_by_id) {
            $event = $event_by_id;
            $event->setSlug('escape-lille-test');
            $event->setDate(new \Datetime);
        }

        //sections
        if(!($s = $event->getSections()) || $s->count() == 0){
            $this->addRegistrationToEvent($manager,$event);

            //sectors
            $section_type = $manager->getRepository(SectionType::class)->findOneBySlug('sectors');
            $section_sectors = new SectionSector();
            $section_sectors->setSectionType($section_type);
            $section_sectors->setTitle($section_type->getTitle());
            $section_sectors->setMenuTitle($section_type->getTitle());
            // OLD ?? $section_sectors->setSorder($section_type->getSorder());
            $section_sectors->setSorder(2);
            $section_sectors->setOnPublic(true);
            $section_sectors->setEvent($event);
            $manager->persist($section_sectors);

            $sector = $manager->getRepository(Sector::class)->find(1); //Administratif / gestion
            $sector2 = $manager->getRepository(Sector::class)->find(5); //Banque / finance

            //to do check if sector already in event
            if(!$event->getSectors()){
                $event->addSector($sector);
                $event->addSector($sector2);
            }

            //partners
        }

        // $this->addExposant($manager,$event);

        $manager->persist($event);
        $manager->flush();

        return $event ;
    }

    // experts lille
    public function createEventExpertsLille($manager)
    {
        $event = $manager->getRepository(EventJobs::class)->findOneById(20000);
        if(!$event){
            $event_type = $manager->getRepository(EventType::class)->findOneByShortName('Experts');
            $event = new EventJobs();
            $event->setType($event_type);
            $place = $manager->getRepository(Place::class)->findOneBy(['slug' => 'lille']); //cci grand lille
            $event->setPlace($place);

            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($manager->getRepository(User::class)->find(2));
            $event->setSlug('experts-lille-test');
            $event->setOnline(new \Datetime);
            $event->setOffline(new \Datetime);
            $event->setClosingAt(new \Datetime);
            $event->setNbStand(20);
            $specBase = $manager->getRepository(SpecBase::class)->findOneBy(['name' => 'Cahier des charges par défaut']);
            $event->setSpecBase($specBase);

            $event->setId(20000); // this is very important for behat test
            $metadata = $manager->getClassMetaData(EventJobs::class);
            $metadata->setIdGenerator(new AssignedGenerator());
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        }

        $event->setSlug('experts-lille-test');
        $event->setDate(new \Datetime);

        //sections
        if(!($s = $event->getSections()) || $s->count() == 0){
            $this->addRegistrationToEvent($manager,$event);

            //sectors
            $section_type = $manager->getRepository(SectionType::class)->findOneBySlug('sectors');
            $section_sectors = new SectionSector();
            $section_sectors->setSectionType($section_type);
            $section_sectors->setTitle($section_type->getTitle());
            $section_sectors->setMenuTitle($section_type->getTitle());
            // OLD ?? $section_sectors->setSorder($section_type->getSorder());
            $section_sectors->setSorder(2);
            $section_sectors->setOnPublic(true);
            $section_sectors->setEvent($event);
            $manager->persist($section_sectors);

        }

        $sector = $manager->getRepository(Sector::class)->find(1); //Administratif / gestion
        $sector2 = $manager->getRepository(Sector::class)->find(5); //Banque / finance

        //to do check if sector already in event
        if($event->getSectors()){
            foreach($event->getSectors() as $sectorEvent) {
                $event->removeSector($sectorEvent);
            }
        }
        $event->addSector($sector);
        $event->addSector($sector2);

        //partners

        // $this->addExposant($manager,$event);

        $manager->persist($event);
        $manager->flush();

        return $event ;
    }

    public function addExposant($manager)
    {
        $exposant = $manager->getRepository(User::class)->findOneByEmail('exposant@l4m.fr');
        $organization = $manager->getRepository(Organization::class)->find(2); //
        $exposant->setOrganization($organization);
        
        $manager->persist($exposant);
        $manager->flush();
        
        $events = [];
        foreach ($this->events_id as $id) {
            array_push($events, $manager->getRepository(Event::class)->find($id));
        }

        foreach ($events as $event) {
            if($event){
                $participations = $manager->getRepository(Participation::class)->findByOrganizationAndEvent($organization,$event);
                // recreons la participation
                if(count($participations) > 0){
                    foreach($participations as $p) {
                        $manager->remove($p);
                    }
                    $manager->flush();
                }

                $manager->persist($organization);
                $participation = $this->participation_factory->get($event, $organization);
                $participation->setResponsable($exposant);
                $participation->setOrganization($organization);
                $participation->setCompanyName('Participation L4M Test');
                $participation->setEvent($event);
                $manager->persist($participation);
                if($participation instanceof ParticipationJobs) {
                    // ajoutons un job
                    $job = new Job();
                    $n = 'Développeur aléatoire '.uniqid();
                    $job->setParticipation($participation);
                    $job->setName($n);
                    $job->setTimeContract('');
                    $job->setContractType($manager->getRepository(ContractType::class)->findOneBySlug('cdd'));
                    $job->setPresentation('Présentation du poste de '.$n);
                    $manager->persist($job);
                }
            }
        }

        $exposant = $manager->getRepository(User::class)->findOneByEmail('exposant2@l4m.fr');
        if(is_null($exposant)) {
            $exposant = new ExposantUser();
            $exposant->setEmail('exposant2@l4m.fr');
            $exposant->setFirstname('Use');
            $exposant->setLastname('Key');
            $exposant->setPhone('0606060606');
            $exposant->setUsername('usekeyexposant');
            $exposant->setPlainPassword('abcd1234');
            $manager->persist($exposant);
            $manager->flush();
        }
        
        $organization = $manager->getRepository(Organization::class)->findOneByName('usekey');

        if(is_null($organization)){
            $organization = new Organization();
            $organization->setName('usekey');
            $type = $manager->getRepository(OrganizationType::class)->find(1);
            $organization->addOrganizationType($type);
            $manager->persist($organization);
            $manager->flush();
        }
        
        $exposant->setOrganization($organization);
        $manager->persist($exposant);
        $manager->flush();
        
        foreach ($events as $event) {
            if($event != null) {
                $participations = $manager->getRepository(Participation::class)->findByOrganizationAndEvent($organization, $event);

                if(count($participations) == 0){
                    $manager->persist($organization);
                    $participation = new ParticipationDefault();
                    $participation->setResponsable($exposant);
                    $participation->setEvent($event);
                    $participation->setOrganization($organization);
                    $participation->setCompanyName('Participation Usekey Test');
                    $manager->persist($participation);
                }
            }
        }
    }

    public function registerCandidateToEvent($manager, Event $event,$candidate=null)
    {
        if(!$candidate){
            $candidate = $manager->getRepository(User::class)->findOneByEmail('candidate@l4m.fr');
        }
        $candidate_participation = $manager->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($candidate,$event);

        if(!$candidate_participation){
            $candidate_participation = new CandidateParticipation();
            $candidate_participation->setCandidate($candidate);
            $candidate_participation->setEvent($event);
            $status = $manager->getRepository(Status::class)->findOneBySlug('registered');
            $candidate_participation->setStatus($status);
            $candidate_participation->setInvitationPath('fixtures');
            $from = ['linkedin','viadeo','twitter','l4m','facebook'];
            $rand = rand(0,4);
            $candidate_participation->setComesFrom($from[$rand]);
            $manager->persist($candidate_participation);
            $manager->flush();
        }

        return $candidate_participation;
    }

    public function loadCandidates($manager)
    {
        $exposant_user = $manager->getRepository(User::class)->findOneByEmail('exposant2@l4m.fr');
        $exposant_scan_user = $manager->getRepository(User::class)->findOneByUsername('l4m.fr');

        $exposants = [$exposant_user, $exposant_scan_user];

        for ($i=0;$i<100;$i++){
            $user = new CandidateUser;
            $name = $this->randomName();
            $uniq_name = $name['firstname'].uniqid();
            $user->setFirstname($name['firstname']);
            $user->setUsername($uniq_name);
            $user->setLastname($name['lastname']);
            $user->setEmail($uniq_name.'@fake.com');
            $user->setSalt('');
            $user->setPhone('0320202020');
            $user->setPlainPassword($name['firstname']);
            $user->setEnabled(true);
            $user->setRoles(array('ROLE_CANDIDATE'));
            $city = $manager->getRepository(City::class)->find(1);
            $user->setCity($city);
            $user->setCv('docx.docx');
            $manager->persist($user);


            foreach ($this->events_id as $id) {
                $event = $manager->getRepository(Event::class)->find($id);

                if($event != null){
                    if(count($event->getCandidateParticipations()) < 100){
                        $candidate_participation = $this->registerCandidateToEvent($manager,$event,$user);

                        $this->createComment($candidate_participation,$exposant_scan_user,$manager,$i);

                        if($i%2 == 0){
                            $this->createComment($candidate_participation,$exposant_user,$manager,$i);
                        }
                    }
                }
            }
        }

        $manager->flush();
    }

    public function createComment(CandidateParticipation $candidate_participation, ClientUser $exposant,$manager,$i)
    {
        $comment = new CandidateParticipationComment();
        $comment->setCandidateParticipation($candidate_participation);
        $comment->setScannedAt(new \Datetime);
        $event = $candidate_participation->getEvent();
        $participation = $manager->getRepository(Participation::class)->findByClientUserAndEvent($exposant,$event);
        $comment->setOrganizationParticipation($participation);

        // + / -random like/favorite
        if ($i < 3 ){
            $comment->setFavorite(true);
        }
        elseif ($i < 6 ){
            $comment->setLike(1);
        }
        elseif ($i < 9 ){
            $comment->setLike(-1);
        }

        $manager->persist($comment);
    }

    public function randomName() {
        $firstname = array(
            'Johnathon',
            'Anthony',
            'Erasmo',
            'Raleigh',
            'Nancie',
            'Tama',
            'Camellia',
            'Augustine',
            'Christeen',
            'Luz',
            'Diego',
            'Lyndia',
            'Thomas',
            'Georgianna',
            'Leigha',
            'Alejandro',
            'Marquis',
            'Joan',
            'Stephania',
            'Elroy',
            'Zonia',
            'Buffy',
            'Sharie',
            'Blythe',
            'Gaylene',
            'Elida',
            'Randy',
            'Margarete',
            'Margarett',
            'Dion',
            'Tomi',
            'Arden',
            'Clora',
            'Laine',
            'Becki',
            'Margherita',
            'Bong',
            'Jeanice',
            'Qiana',
            'Lawanda',
            'Rebecka',
            'Maribel',
            'Tami',
            'Yuri',
            'Michele',
            'Rubi',
            'Larisa',
            'Lloyd',
            'Tyisha',
            'Samatha',
        );

        $lastname = array(
            'Mischke',
            'Serna',
            'Pingree',
            'Mcnaught',
            'Pepper',
            'Schildgen',
            'Mongold',
            'Wrona',
            'Geddes',
            'Lanz',
            'Fetzer',
            'Schroeder',
            'Block',
            'Mayoral',
            'Fleishman',
            'Roberie',
            'Latson',
            'Lupo',
            'Motsinger',
            'Drews',
            'Coby',
            'Redner',
            'Culton',
            'Howe',
            'Stoval',
            'Michaud',
            'Mote',
            'Menjivar',
            'Wiers',
            'Paris',
            'Grisby',
            'Noren',
            'Damron',
            'Kazmierczak',
            'Haslett',
            'Guillemette',
            'Buresh',
            'Center',
            'Kucera',
            'Catt',
            'Badon',
            'Grumbles',
            'Antes',
            'Byron',
            'Volkman',
            'Klemp',
            'Pekar',
            'Pecora',
            'Schewe',
            'Ramage',
        );

        $first_name = $firstname[rand ( 0 , count($firstname) -1)];
        $last_name = $lastname[rand ( 0 , count($lastname) -1)];

        return ['firstname' => $first_name, 'lastname' => $last_name];
    }

    public function addRegistrationToEvent($manager,Event $event)
    {
        //registration
        $section_type = $manager->getRepository(SectionType::class)->findOneBySlug('registration');
        $section_registration = new SectionSimple();
        $section_registration->setSectionType($section_type);
        $section_registration->setTitle($section_type->getTitle());
        $section_registration->setMenuTitle($section_type->getTitle());
        // OLD ?? $section_registration->setSorder($section_type->getSorder());
        $section_registration->setSorder(2);
        $section_registration->setOnPublic(true);
        $section_registration->setEvent($event);
        $manager->persist($section_registration);
    }

    public function addContactToEvent($manager,Event $event)
    {
        //contact
        $section_type = $manager->getRepository(SectionType::class)->findOneBySlug('contact');
        $section_contact = new SectionSimple();
        $section_contact->setSectionType($section_type);
        $section_contact->setMenuTitle($section_type->getTitle());
        $section_contact->setTitle($section_type->getTitle());
        $section_contact->setSorder(3);
        $section_contact->setOnPublic(true);
        $section_contact->setEvent($event);
        $manager->persist($section_contact);
    }

    public function addJobJoblinkAndJoblinkSession($manager, $event, $candidateParticipation)
    {
        if($event) {
            $participation = new ParticipationJobs();
            $participation->setCompanyName("testAjoutCandidateToJoblinkSession");
            $participation->setSlug('Test escape');
            $participation->setEvent($event);

            $manager->persist($participation);
            $manager->flush();

            $job = new Job();
            $job->setName('test job name');
            $job->setPresentation('Ceci est la présentation d\'un test de job');
            $job->setTimeContract('');
            $job->addCandidate($candidateParticipation);

            $manager->persist($job);
            $manager->flush();

            $joblink = new Joblink();
            $joblink->setName('Ceci est un test de joblink name');
            $joblink->setSlug('test de joblink slug');
            $joblink->addEvent($event);

            $manager->persist($joblink);
            $manager->flush();

            $joblinkSession = new JoblinkSession();
            $joblinkSession->setStart(new \DateTime('12:00:00'));
            $joblinkSession->setEnd(new \DateTime('14:00:00'));
            $joblinkSession->setJoblink($joblink);
            $joblinkSession->setParticipation($participation);

            $manager->persist($joblinkSession);
            $manager->flush();
        }
    }
    public static function getGroups(): array
    {
        return ['group1', 'group2'];
    }
}