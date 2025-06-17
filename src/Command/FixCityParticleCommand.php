<?php

namespace App\Command;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCityParticleCommand extends Command
{
    protected static $defaultName = 'fix:city';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('fix city particle "le"')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $cities = $this->em->getRepository(City::class)->findByChars('Le');

        foreach ($cities as $city) {
            $city_name = $city->getName();
            $after = substr($city_name, 2, strlen($city_name));

            if(ctype_upper($after[0])){
                $city_name = "Le ".$after ;
                $city->setName($city_name);
                $io->text($city->getName());
            }            
        }

        $this->em->flush();
        $io->success('City particle fixed');
    }
}