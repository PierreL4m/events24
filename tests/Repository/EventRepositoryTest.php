<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Event;
use App\Repository\EventRepository;

class EventRepositoryTest extends KernelTestCase 
{
   /**
    * @var \Doctrine\ORM\EntityManager
    */
   private $entityManager;
   
   /**
    * {@inheritDoc}
    */
  protected function setUp() :void
   {
       $kernel = self::bootKernel();

       $this->entityManager = $kernel->getContainer()
           ->get('doctrine')
           ->getManager();
   }
   
   public function testFindRecall() 
   {
       
       /**
        * 
        * @var EventRepository $repository
        */
       $repository = $this->entityManager->getRepository(Event::class);
       
       /**
        * 
        * @var Event $event
        */
       $event =
            $repository
            ->createQueryBuilder('e')->orderBy('e.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
       $orig_date = (($d = $event->getDate()) ? clone $d : null);
       $orig_recall_date = (($d = $event->getFirstRecallDate()) ? clone $d : null);
       
       $date  = new \DateTime();
       $date_passee  = new \DateTime(date('Y-m-d', time()-86400));
       $date_recall = new \DateTime(date('Y-m-d'));
       $date_future = new \DateTime('2051-12-06 00:00:00');
       
       // if event has passed date AND recall date == now, should be null
       $event->setDate($date_passee);
       $event->setFirstRecallDate($date_recall);
       $this->entityManager->persist($event);
       $this->entityManager->flush();
       $r = $repository->findRecall();
       $this->assertNotContains($event, $r, 'L\'événement devrait être concerné');
       
       
       // if event has future date or present date AND passed recall date, should be null
       $event->setDate($date);
       $event->setFirstRecallDate($date_passee);
       $this->entityManager->persist($event);
       $this->entityManager->flush();
       $r = $repository->findRecall();
       $this->assertNotContains($event, $r);
       
       
       // if event has future date AND date == now, should return event
       $event->setDate($date_future);
       $event->setFirstRecallDate($date_recall);
       $this->entityManager->persist($event);
       $this->entityManager->flush();
       $r = $repository->findRecall();
       $this->assertContains($event, $r);
       
       // let's save original data back into db
       $event->setDate($orig_date);
       $event->setFirstRecallDate($orig_recall_date);
       $this->entityManager->persist($event);
       $this->entityManager->flush();
   }
   
   /**
    * {@inheritDoc}
    */
   protected function tearDown() :void
   {
       parent::tearDown();

       $this->entityManager->close();
       $this->entityManager = null; // avoid memory leaks
   }
}

