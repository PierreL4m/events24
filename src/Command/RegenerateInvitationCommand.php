<?php

namespace App\Command;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RegenerateInvitationCommand extends Command
{
    protected static $defaultName = 'regenerate:invitations';

    private $em;
    private $helper;

    public function __construct(MailerHelper $mailer, RenderHelper $helper, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;   
        $this->helper =  $helper;
    }


    protected function configure()
    {
        $this
            ->setDescription('Send recall mails to candidates')
            ->addArgument('event_id', InputArgument::REQUIRED , 'Set event_id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('event_id');
        $io = new SymfonyStyle($input, $output);
        $event = $this->em->getRepository(Event::class)->find($id);
   
        if(!$event){
             $io->error('no event for id :'.$id);
             return;
        }
       
        $participations = $this->em->getRepository(CandidateParticipation::class)->findRecallByEvent($event);
           
        foreach ($participations as $p) {
            $this->helper->generateInvitation($p);
            $io->text($p->getCandidate());
        }

        $io->success('invitations have been regenerated');
    }
}
