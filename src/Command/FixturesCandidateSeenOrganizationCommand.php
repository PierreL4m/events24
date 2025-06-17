<?php

namespace App\Command;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Mobility;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixturesCandidateSeenOrganizationCommand extends Command
{
    protected static $defaultName = 'candidate:load:fixtures';   
  
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->em;
        //move amiens 2018 to 31/12/2018
        $amiens = $this->em->getRepository(Event::class)->find(80);
        $date = new \Datetime('2018-12-31');
        $amiens->setDate($date);

        //move le_havre 2018 to 20/12/2018
        $le_havre = $event = $this->em->getRepository(Event::class)->find(79);
        $date1 = new \Datetime('2018-12-20');
        $le_havre->setDate($date1);

        $em->flush();

        $name = 'test_seen_exposant';       
        $candidate = $em->getRepository(CandidateUser::class)->findOneByUsername($name);

        if (!$candidate){
            $candidate = new CandidateUser();
            $candidate->setFirstname($name);
            $candidate->setUsername($name);
            $candidate->setLastname($name);
            $candidate->setEmail($name.'@l4m.fr');
            $candidate->setPhone('0320202020');
            $candidate->setPlainPassword($name);
            $candidate->setEnabled(true);
            $candidate->setRoles(array('ROLE_CANDIDATE'));
            $candidate->setMailingEvents(true);               
            $city = $em->getRepository(City::class)->find(1);
            $candidate->setCity($city);
            $em->persist($candidate);
            $em->flush();
        }

        $candidate_participation = new CandidateParticipation();
        $candidate_participation->setCreatedAt(new \Datetime());
        $candidate_participation->setEvent($amiens);
        $candidate->addCandidateparticipation($candidate_participation);
        $em->persist($candidate_participation);

        $candidate_participation2 = new CandidateParticipation();
        $candidate_participation2->setCreatedAt(new \Datetime());
        $candidate_participation2->setEvent($le_havre);
        $candidate->addCandidateparticipation($candidate_participation2);
        $em->persist($candidate_participation);

        $candidate_participation3 = new CandidateParticipation();
        $candidate_participation3->setCreatedAt(new \Datetime());
        $candidate_participation3->setEvent($this->em->getRepository(Event::class)->find(70));
        $candidate->addCandidateparticipation($candidate_participation3);
        $em->persist($candidate_participation3);

        $em->flush();

        $participations = array();
        $organization_participation_afec_amiens = $em->getRepository(Participation::class)->find(3483);
        array_push($participations, $organization_participation_afec_amiens);
        $organization_participation_alse_amiens = $em->getRepository(Participation::class)->find(3481);
        array_push($participations, $organization_participation_alse_amiens);
      //  candidatecomment

        foreach ($participations as $p) {
            $comment = new CandidateParticipationComment();
            $comment->setScannedAt(new \Datetime);
            $comment->setCandidateParticipation($candidate_participation);
            $comment->setOrganizationParticipation($p);
            $em->persist($comment);
            $em->flush();
        }

        
        $organization_participation_apei_boulogne = $em->getRepository(Participation::class)->find(2979);
        
        $comment = new CandidateParticipationComment();
        $comment->setScannedAt(new \Datetime);
        $comment->setCandidateParticipation($candidate_participation3);
        $comment->setOrganizationParticipation($organization_participation_apei_boulogne);
        $em->persist($comment);
        $em->flush();
        
        $io->success('Fixtures generated');
    }
}
