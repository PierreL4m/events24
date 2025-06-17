<?php

namespace App\Command;

use App\Entity\CandidateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixLastnameCommand extends Command
{
    protected static $defaultName = 'fix:lastnames';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $candidates = $this->em->getRepository(CandidateUser::class)->findAll();

        foreach ($candidates as $candidate) {               
            $candidate->setLastName($candidate->getLastName());
            $io->text($candidate);
        }   
        $this->em->flush();        
    }
}
