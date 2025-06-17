<?php

namespace App\Tests\Controller;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Status;
use App\Entity\EventType;
use App\Entity\Job;
use App\Entity\Joblink;
use App\Entity\JoblinkSession;
use App\Repository\JoblinkSessionRepository;
use App\Repository\CandidateParticipationRepository;
use App\Tests\AuthentificationAndFilesHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;
use Symfony\Component\Mime\Email;

class RecruitmentControllerTest extends AuthentificationAndFilesHelper
{
    public function testChangeStatus()
    {
        $event_type = $this->em->getRepository(EventType::class)->findOneBy(
            array('registrationValidation' => EventType::REGISTRATION_VALIDATION_VIEWER)
        );
        $event = $this->em->getRepository(Event::class)->getNextEventByType($event_type);
        $candidate = $this->em->getRepository(CandidateUser::class)->findOneByUsername('candidate');
        //candidate has participation experts in fixtures
        $candidate_participation =  $this->em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($candidate,$event);
        $participation_status = $candidate_participation->getStatus();
        $all_status =  $this->em->getRepository(Status::class)->findAll();
        $status = new ArrayCollection();
        foreach($all_status as $k => $s) {
            if($s->getId() != $participation_status->getId()) {
//                 echo "S ID ".$s->getId()." not egal ".$participation_status->getId()."\n";
                $status[$k] = $s;
            }
        }
        foreach ($status as $val) {
            $crawler = $this->client->request('GET', '/admin-recruteurs/changer-statut/'.$candidate_participation->getId());
            $this->assertSame(200, $this->client->getResponse()->getStatusCode());

            $form = $crawler->selectButton('save')->form();
            $form['change_status[status]'] = $val->getId();
            $crawler = $this->client->submit($form);

            if($val->getSlug() != 'waiting' && $val->getSlug() != 'pending' && $val->getSlug() != 'registered'){
                // checks that an email was sent
                $this->assertEmailCount(1);
                /**
                 *
                 * @var \Symfony\Component\Mime\Email $message
                 */
                $message = $this->getMailerMessage();
                // Asserting email data
                $this->assertInstanceOf(Email::class, $message);
                $attachments = count($message->getAttachments());

                switch ($val->getSlug()) {
                    case 'refused_after_call':
                        $this->assertStringContainsString(' n\'a pas été retenue', $message->getSubject());
                        $this->assertEmailHtmlBodyContains($message, 'Suite à notre conversation téléphonique');
                        break;
                    case 'refused':
                        $this->assertStringContainsString(' n\'a pas été retenue', $message->getSubject());
                        $this->assertEmailHtmlBodyNotContains($message, 'Suite à notre conversation téléphonique');
                        break;

                    case 'confirmed':
                        $this->assertStringContainsString('invitation', $message->getSubject());
                        break;

                    default:
                        throw new \Exception('Error status in array status has no case in switch');
                        break;
                }

                if($val->getSlug() == 'confirmed'){
                    $this->assertEquals(1,$attachments);
                }
                else{
                    $this->assertEquals(0,$attachments);
                }

                $this->assertEmailHtmlBodyContains($message, 'Bonjour Candidate CANDIDATE');
                $tos = $message->getTo();
                $this->assertEquals(1,count($tos));
                $this->assertSame('candidate@l4m.fr', $tos[0]->getAddress());
            }
            else{ //case waiting no mail sent
                $this->assertEmailCount(0);
            }
            //$this->client->followRedirect();
            //$this->assertSame($val, $candidate_participation->getStatus()->getSlug()); this fail to do real check

        }
    }

    private function addOrDeleteCandidateToJoblinkSession($valueIdButton, $joblinkSessionId, $event)
    {
        $crawlerRequest = $this->client->request('GET', '/admin-recruteurs/candidates-list/' . $event->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawlerRequest->selectButton($valueIdButton)->form();
        $form['joblink_session']->select($joblinkSessionId);

        $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin-recruteurs/candidates-list/' . $event->getId())
        );
    }

    public function testAddAndDeleteCandidateToJoblinkSession()
    {
        $eventJob = $this->em->getRepository(EventType::class)->findOneBy(
            ['registrationType' => EventType::REGISTRATION_TYPE_JOB],
            ['id' => 'DESC']
        );

        $event = $this->em->getRepository(Event::class)->findOneBy(
            ['type' => $eventJob],
            ['id' => 'DESC']
        );

        if($event) {
            $candidateParticipation = $this->em->getRepository(CandidateParticipation::class)->findOneBy(
                ['event' => $event->getId()],
                ['id' => 'DESC']
            );

            $joblinks = $event->getJoblinks();

            if ($joblinks) {
                $joblinkSession = $this->em->getRepository(JoblinkSession::class)->findOneBy(
                    ['joblink' => $joblinks[0]],
                    ['id' => 'DESC']
                );

                if (!empty($candidateParticipation->getJoblinkSessions())) {
                    foreach ($candidateParticipation->getJoblinkSessions() as $j) {
                        $candidateParticipation->removeJoblinkSession($j);
                    }
                }

                $this->addOrDeleteCandidateToJoblinkSession('enregistrer', $joblinkSession->getId(), $event);

                $candidate = $this->em->getRepository(CandidateParticipation::class)->findOneBy(
                    ['id' => $candidateParticipation->getId()]
                );

                // FIXME ?
                $this->assertEquals(json_encode($candidate->getJoblinkSessions()), json_encode($candidateParticipation->getJoblinkSessions()));

                $this->addOrDeleteCandidateToJoblinkSession('supprimer', $joblinkSession->getId(), $event);
            }
        }
    }

}