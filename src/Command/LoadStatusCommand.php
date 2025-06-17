<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\Status;
use App\Helper\GlobalHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadStatusCommand extends Command
{
    // php bin/console load:status 20000
    protected static $defaultName = 'load:status';
    private $em;
    private $helper;

    public function __construct(EntityManagerInterface $em, GlobalHelper $helper)
    {
        parent::__construct();
        $this->em = $em;
        $this->helper = $helper;
    }


    protected function configure()
    {
        $this
            ->setDescription('load status for candidates in event_ids')
            ->addArgument('event_id', InputArgument::REQUIRED, 'event_id to load candidate status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('event_id');

        if ($id) {
            $io->note(sprintf('event_id= %s', $id));
        }

        $event = $this->em->getRepository(Event::class)->find($id);

        foreach ($event->getCandidateParticipations() as $cp){
            $rand = rand('0', '4');
            $status = array('registered', 'refused', 'refused_after_call', 'confirmed','confirmed');
            $status_slug = $status[$rand];
            $status = $this->em->getRepository(Status::class)->findOneBySlug($status_slug);
            $cp->setStatus($status);

            if($status_slug == 'confirmed'){
                $this->helper->generateQrCode($cp);
                $rand = rand('0', '1');
                if ($rand == 0){
                    $cp->setScannedAt(new \DateTime());
                }
                if ($manager = $event->getManager()){
                    $cp->setHandledBy($manager);
                }
                $rand = rand(0,1);
                $cp->getCandidate()->setPhoneRecall($rand);
            }
            $this->em->flush();

            $io->text($cp->getCandidate());
        }

        $io->success('status load for event_id='.$id);


    }


    public function loadStatus($slug,$io)
    {
        $io->text($slug);
    }


}
