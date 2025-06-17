<?php

namespace App\Command;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlaceMobileNameCommand extends Command
{
    protected static $defaultName = 'generate:mobile-places';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    protected function configure()
    {
        $this
            ->setDescription('generate mobile name for places')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $places = $this->em->getRepository(Place::class)->findAll();

        foreach ($places as $place) {   
            $io->text($place);             
            $place->setNameMobile($place->getName());
        }   
        $this->em->flush();        
    }
}
