<?php

namespace App\Command;

use App\Entity\Sector;
use App\Helper\GlobalHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateSectorSlugCommand extends Command
{
    protected static $defaultName = 'sector:slug';

    private $em;
    private $helper;

    public function __construct(EntityManagerInterface $em, GlobalHelper $helper)
    {
        parent::__construct();
        $this->em = $em;
        $this->helper = $helper;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $sectors = $this->em->getRepository(Sector::class)->findAll();

        foreach ($sectors as $sector) {   
            $io->text($sector);             
            $sector->setSlug($this->helper->generateSlug($sector->getName()));
        }   
        $this->em->flush();
        
    }
}
