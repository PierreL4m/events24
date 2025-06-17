<?php

namespace App\Tests\Command;

use App\Entity\Event;
use App\Entity\EventSimple;
use App\Entity\Place;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use App\Entity\EventType;
use App\Entity\SpecBase;



class ArnaudRecallCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp() :void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testExecute()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $application = new Application($kernel);

        $command = $application->find('arnaud:recall');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Arnaud recall ok', $output);
    }

    public function testRepository()
    {
        $user = $this->em->getRepository(User::class)->findOneByUsername('france');

        $monday = new \Datetime('2018-08-06');
        $tuesday = new \Datetime('2018-08-07');
        $wednesday = new \Datetime('2018-08-08');
        $thursday = new \Datetime('2018-08-09');
        $friday = new \Datetime('2018-08-10');
        $saturday = new \Datetime('2018-08-11');
        $sunday = new \Datetime('2018-08-12');

        $no_recall = array(
            $tuesday,
            $thursday,
            $friday,
            $saturday,
            $sunday
        );
        
        $spec_base = $this->em->getRepository(SpecBase::class)->findDefault();

        $event = new EventSimple();
        $event->setManager($user);
        $event->setSlug('event-test');
        $event->setDate(new \Datetime('2018-08-09'));//thursday
        $event->setOnline($tuesday);
        $event->setOffline($tuesday);
        $event->setClosingAt($tuesday);
        $event->setPlace($this->em->getRepository(Place::class)->find(9));//pasino
        $event->setType($this->em->getRepository(EventType::class)->findOneByShortName('24'));//24
        $event->setSpecBase($spec_base);
        
        
        $this->em->persist($event);

        $event2 = clone($event);        
        $event->setSlug('event-test2');
        $event2->setPlace($this->em->getRepository(Place::class)->find(10));//compiègne
        $event2->setDate(new \Datetime('2018-08-14')); //next tuesday

        $this->em->persist($event2);
        $this->em->flush();

        $events = $this->em->getRepository(Event::class)->findD3RecallArnaud($monday);
        $this->assertEquals(1,count($events));
        $events = $this->em->getRepository(Event::class)->findD3RecallArnaud($wednesday);
        $this->assertEquals(1,count($events));

        foreach ($no_recall as $day) {
            $events = $this->em->getRepository(Event::class)->findD3RecallArnaud($day);
            $this->assertEquals(0,count($events));
        }
        $this->em->remove($event);
        $this->em->remove($event2);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
