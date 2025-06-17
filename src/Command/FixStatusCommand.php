<?php

namespace App\Command;

use App\Entity\CandidateParticipation;
use App\Entity\EventSimple;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixStatusCommand extends Command
{
    protected static $defaultName = 'fix:status';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('set 24 candidates participations to confirmed')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $participations = $this->em->getRepository(CandidateParticipation::class)->findAll();
		$status = $this->em->getRepository(Status::class)->findOneBySlug('confirmed');

        foreach ($participations as $participation) {
            if ($participation->getEvent()->getType()->registrationValidationAuto()){
                $io->text($participation->getCandidate());
                $participation->setStatus($status);
            }
            else{
                $io->text('EXPERT participation_id='.$participation->getId());
            }
        }

        $this->em->flush();
        $io->success('Event simple status ok');
    }
}