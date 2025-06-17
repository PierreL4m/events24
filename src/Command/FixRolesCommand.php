<?php

namespace App\Command;

use App\Entity\ExposantUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\RhUser;

class FixRolesCommand extends Command
{
    protected static $defaultName = 'fix:roles';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }


    protected function configure()
    {
        $this
            ->setDescription('fix organization roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $exposant_users = $this->em->getRepository(ExposantUser::class)->findAll();

        foreach ($exposant_users as $exposant) {   
            $io->text($exposant);             
            $exposant->setRoles(['ROLE_ORGANIZATION']);
        }   
        $this->em->flush();
        
        $rh_users = $this->em->getRepository(RhUser::class)->findAll();
        foreach ($rh_users as $rh) {
            $io->text($rh);
            $rh->setRoles(['ROLE_RH']);
        }
        $this->em->flush();
        
    }
}
