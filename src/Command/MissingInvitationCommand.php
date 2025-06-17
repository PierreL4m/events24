<?php

namespace App\Command;

use App\Entity\Candidate;
use App\Entity\Event;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\CandidateParticipation;

class MissingInvitationCommand extends Command
{
    protected static $defaultName = 'fix:invitations';

    private $em;
    private $mailer;
    private $helper;
    private $project_dir;

    public function __construct(MailerHelper $mailer, RenderHelper $helper, $project_dir)
    {
        parent::__construct();
        $this->em = $mailer->getEm();       
        $this->mailer = $mailer;
        $this->helper = $helper;
        $this->project_dir = $project_dir;
    }


    protected function configure()
    {
        $this
            ->setDescription('send missing invitation')
            ->addArgument('first_fix',  InputArgument::OPTIONAL, 'set candidate path to null if file not found')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        if ($input->getArgument('first_fix')){
            /**
             *
             * @var CandidateParticipation[] $participations
             */
            $participations = $this->em->getRepository(CandidateParticipation::class)->findAll();

            foreach ($participations as $participation) {      
                if ($participation->getinvitationPath() && !$this->helper->fileExists($participation)){
                    $io->text($participation->getCandidate());
                    $participation->setInvitationPath(null);
                }
            }            
            $this->em->flush();

            $participations = $this->em->getRepository(CandidateParticipation::class)->findNoinvitationFutureEvents();
            $io->text(count($participations).' participations restantes');   
        }
        else{
            $participations = $this->em->getRepository(CandidateParticipation::class)->findNoinvitationFutureEvents();
            $io->text(count($participations).' participations');   

            foreach ($participations as $participation) {   
                $io->text($participations->getCandidate()." ".$participation->getEvent());             
                $this->helper->generateInvitation($participation);

                if ($this->helper->fileExists($participation)){
                    $this->mailer->sendInvitation($participation);
                }
            }   
            $this->em->flush();
        }
    }
}
