<?php

namespace App\Command;

use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CandidateRecallCommand extends Command
{
    protected static $defaultName = 'candidate:recall';

    private $em;
    private $mailer;
    private $helper;

    public function __construct(MailerHelper $mailer, RenderHelper $helper)
    {
        parent::__construct();
        $this->em = $mailer->getEm();       
        $this->mailer = $mailer;
        $this->helper =  $helper;
    }


    protected function configure()
    {
        $this
            ->setDescription('Send recall mails to candidates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $events = $this->em->getRepository(Event::class)->findRecallArnaud(-1);
   //debug $events = [ $this->em->getRepository(Event::class)->find(80)] ;
        if(count($events) > 0){
            $this->mailer->sendMail(
                'pierre.schuvey@l4m.fr',
                'invitation should have been resent',
                'recall_event_list',
                array('events' => $events),
                "webmaster@l4m.fr"
            );
        }
        else{
            $io->success('no recall invitation to sent');
            return;
        }

        foreach ($events as $event) {
            /* OLD 
            $candidates = $this->em->getRepository(Candidate::class)->findRecallByEvent($event);
           
            foreach ($candidates as $candidate) {
                 if (!$this->helper->fileExistsOld($candidate)){
                    $this->helper->generateInvitationOld($candidate);
                }
                $io->text($candidate);
                $this->mailer->sendInvitationOld($candidate, null, true);
            }
            */
            $participations = $this->em->getRepository(CandidateParticipation::class)->findRecallByEvent($event);
            foreach ($participations as $p) {
                 if (!$this->helper->fileExists($p)){
                    $this->helper->generateInvitation($p);
                }
                
                try {
                    $this->mailer->sendInvitation($p, true);
                    $io->text($p->getCandidate());
                }
                catch(\Exception $e) {
                    $io->error($p->getCandidate());
                }
                
            }
       
        }
        $io->success('recall mails sent');
    }
}
