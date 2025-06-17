<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetRefusedCommand extends Command
{
    protected static $defaultName = 'set:refused';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    protected function configure()
    {
        $this
            ->setDescription('Set registered candidates to refused after event')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        $date = new \DateTime();
        date_sub($date,date_interval_create_from_date_string("1 days"));

        $events_yesterday = $this->em->getRepository(Event::class)->findByDate($date);
        //$events_yesterday = [$this->em->getRepository(Event::class)->find(30000)];

        if(count($events_yesterday) > 0) {
            $refused = $this->em->getRepository(Status::class)->findOneBySlug('refused');
            foreach($events_yesterday as $event){
                $io->comment($event);
                foreach ($event->getCandidateParticipations() as $cp){

                    if (!$cp->getStatus() || $cp->getStatus()->getSlug() == 'registered'){
                        $cp->setStatus($refused);
                        $io->text($cp->getCandidate());
                    }
                }
            }
            $this->em->flush();
        }
        $io->success('status set to refused');

    }
}
