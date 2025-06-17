<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixParticipationTimestampCommand extends Command
{
    protected static $defaultName = 'fix:timestamp';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $event = $this->em->getRepository(Event::class)->find(81);
        $morgane = $this->em->getRepository(User::class)->find(694);

        foreach ($event->getParticipations() as $participation) {
            $timestamp = $participation->getTimestamp();
            if($timestamp->getUpdatedBy() == $morgane){
                $timestamp->setUpdatedBy(null);
                $timestamp->setUpdated(null);
            }
        }

        $this->em->flush();
        $io->success('timestamps fixed');
    }
}