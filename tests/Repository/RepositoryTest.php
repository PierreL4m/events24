<?php

namespace App\Tests\Functions;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\EventType;
use App\Entity\Event;
use App\Entity\CandidateUser;
use App\Entity\CandidateParticipation;
use App\Repository\CandidateUserRepository;
use App\Repository\CandidateParticipationRepository;

class RepositoryTest extends KernelTestCase
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

    /**
     * Tests CandidatParticipationRespository with differents contexts and registration validation types
     */
    public function testCandidateParticipation()
    {
        /**
         * 
         * @var CandidateUserRepository $er
         */
        $er = $this->em->getRepository(CandidateUser::class);
        /**
         * 
         * @var CandidateParticipationRepository $cer
         */
        $cer = $this->em->getRepository(CandidateParticipation::class);
        
        $events = $this->em->getRepository(Event::class)->findPastEvents(20);
        $event_auto = $event_validation = null;
        foreach($events as $event) {
            if($event->getType()->registrationValidationAuto()) {
                if(!$event_auto) {
                    $candidates = $er->findByEvent($event);
                    if(count($candidates) > 10) {
                        $event_auto = $event;
                    }
                }
            }
            else {
                if(!$event_validation) {
                    $candidates = $er->findByEvent($event);
                    if(count($candidates) > 10) {
                        $event_validation = $event;
                    }
                }
            }
            
            if($event_auto && $event_validation) {
                break;
            }
        }
        
        // event with auto validation
        $this->assertNotNull($event_auto, 'No event with auto validation found.');
        // combien de candidats
        $candidates = $er->findByEvent($event_auto);
        $this->assertNotNull($candidates, 'No candidate found for event with auto validation.');
        $nb_candidates = count($candidates);
        // confirmed candidates count
        $candidates = $er->findByEvent($event_auto, ['status' => 'confirmed']);
        $this->assertNotNull($candidates, 'No confirmed candidate found for event with auto validation..');
        $this->assertEquals($nb_candidates, count($candidates), 'For an event with auto validation, filterd confirmed candidates should equel not filtered candidates');
        
        // confirmed candidates with mailingRecall
        $candidates = $er->findByEvent($event_auto, ['status' => 'confirmed', 'mailing_recall' => true]);
        $this->assertNotNull($candidates, 'No confirmed candidate with mailingRecall.');
        $nb_candidates = count($candidates);
        // participations recalls to send 
        $partipations = $cer->findRecallByEvent($event_auto);
        $this->assertNotNull($partipations, 'No participation for mailingRecall found.');
        $this->assertEquals($nb_candidates, count($partipations), 'For an event with auto validation, participations to recall count should equall confirmed candidates count.');
        
        // event with validation
        $this->assertNotNull($event_validation, 'No event without auto validation found.');
        // candidates
        $candidates = $er->findByEvent($event_validation);
        $this->assertNotNull($candidates, 'No candidate found for event without auto validation.');
        $nb_candidates = count($candidates);
        // confirmed candidates
        $candidates = $er->findByEvent($event_validation, ['status' => 'confirmed']);
        $this->assertNotNull($candidates, 'No confirmed candidate found for event without auto validation.');
        $this->assertGreaterThan(count($candidates), $nb_candidates, 'For an event without auto validation , candidates count should be greater than confirmed candidates count.');
        
        // confirmed candidates with mailingRecall
        $candidates = $er->findByEvent($event_validation, ['status' => 'confirmed', 'mailing_recall' => true]);
        $this->assertNotNull($candidates, 'No confirmed candidate with mailingRecall for event without auto validation.');
        $nb_candidates = count($candidates);
        // participations recalls to send
        $partipations = $cer->findRecallByEvent($event_validation);
        $this->assertNotNull($partipations, 'No participation for mailingRecall found for event without auto validation.');
        $this->assertEquals($nb_candidates, count($partipations), 'For an event without auto validation, participations to recall count should equall confirmed candidates count.');
        
        
        
        
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() :void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}