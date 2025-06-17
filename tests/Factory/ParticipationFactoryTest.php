<?php

// namespace App\Tests\Factory;

// use App\Entity\Event;
// use App\Entity\EventJobs;
// use App\Entity\EventSimple;
// use App\Entity\EventType;
// use App\Entity\Organization;
// use App\Entity\OrganizationType;
// use App\Entity\Place;
// use App\Factory\ParticipationFactory;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// class ParticipationFactoryTest extends WebTestCase
// {
// 	 private $em;
// 	 private $container;
// 	 private $session;

//     /**
//      * {@inheritDoc}
//      */
//     protected function setUp() :void
//     {
//         $this->container = $this->client->getContainer();
//         $this->em = $this->container->get('doctrine.orm.entity_manager');
//         $this->session = $this->container->get('session');
//     }

//     public function testGetParticipationAndGetEvent()
//     {
// 		//test event factory
// 		$city24 = $this->em->getRepository(Place::class)->find(1);
// 		$this->assertEquals('48', $city24->getEventType());

// 		$city24 = $this->em->getRepository(Place::class)->find(2);
// 		$this->assertEquals('24', $city24->getEventType());

// 		$cityExperts = $this->em->getRepository(Place::class)->find(14);
// 		$this->assertEquals('Experts', $cityExperts->getEventType());
      
//       	$event_factory = $this->container->get('App\Factory\EventFactory');      
//       	$event24 = $event_factory->get($city24);
//       	$this->assertEquals('App\Entity\EventSimple', get_class($event24));
//       	$event24->setPlace($city24);

//       	$eventExperts = $event_factory->get($cityExperts);
//       	$this->assertEquals('App\Entity\EventJobs', get_class($eventExperts));
//     	$eventExperts->setPlace($cityExperts);

//     	//test participation factory and danger in flashbag
// 		$company_type = $this->em->getRepository(OrganizationType::class)->find(1);
// 		$this->assertEquals('company', $company_type->getSlug());

// 		$formation_type = $this->em->getRepository(OrganizationType::class)->find(2);
// 		$this->assertEquals('formation', $formation_type->getSlug());

// 		$default_type = $this->em->getRepository(OrganizationType::class)->find(3);
// 		$this->assertEquals('autre', $default_type->getSlug());

// 		$company = new Organization();
// 		$company->addOrganizationType($company_type);

// 		$formation = new Organization();
// 		$formation->addOrganizationType($formation_type);

// 		$company_formation = new Organization();
// 		$company_formation->addOrganizationType($company_type);
// 		$company_formation->addOrganizationType($formation_type);

// 		$other = new Organization();
// 		$other->addOrganizationType($default_type);
		
// 		$factory = $this->container->get('App\Factory\ParticipationFactory');
// 		$flash_bag = $this->session->getFlashBag();

// 		//event simple
// 		$participation = $factory->get($event24,$company);
// 		$this->assertEquals('App\Entity\ParticipationCompanySimple', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$participation = $factory->get($event24,$formation);
// 		$this->assertEquals('App\Entity\ParticipationFormationSimple', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$participation = $factory->get($event24,$other);
// 		$this->assertEquals('App\Entity\ParticipationDefault', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);
	
		
// 		$participation = $factory->get($event24,$company_formation);
// 		$this->assertEquals('App\Entity\ParticipationDefault', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),true);
// 		$flash_bag->clear();
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$event24->addOrganizationType($company_type);
// 		$participation = $factory->get($event24,$company_formation);
// 		$this->assertEquals('App\Entity\ParticipationCompanySimple', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$event24->addOrganizationType($formation_type);
// 		$participation = $factory->get($event24,$company_formation);
// 		$this->assertEquals('App\Entity\ParticipationDefault', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),true);
// 		$flash_bag->clear();
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$event24->removeOrganizationType($company_type);
// 		$participation = $factory->get($event24,$company_formation);
// 		$this->assertEquals('App\Entity\ParticipationFormationSimple', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);
		
// 		//event jobs
// 		$participation = $factory->get($eventExperts,$company);
// 		$this->assertEquals('App\Entity\ParticipationJobs', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$participation = $factory->get($eventExperts,$formation);
// 		$this->assertEquals('App\Entity\ParticipationDefault', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),true);
// 		$flash_bag->clear();
// 		$this->assertEquals($flash_bag->has('danger'),false);

// 		$participation = $factory->get($eventExperts,$other);
// 		$this->assertEquals('App\Entity\ParticipationDefault', get_class($participation));
// 		$this->assertEquals($flash_bag->has('danger'),true);
// 		$flash_bag->clear();
// 		$this->assertEquals($flash_bag->has('danger'),false);


//      }
// }
