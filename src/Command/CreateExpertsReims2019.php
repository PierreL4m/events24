<?php

namespace App\Command;

use App\Entity\EventJobs;
use App\Entity\Place;
use App\Entity\SectionSimple;
use App\Entity\SectionType;
use App\Entity\Sector;
use App\Entity\User;
use App\Helper\GlobalHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\EventType;

class CreateExpertsReims2019 extends Command
{
    protected static $defaultName = 'experts:create';

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

        $event = $this->em->getRepository(EventJobs::class)->findOneBySlug('lille-2019');

        if(!$event){
            $event = new EventJobs();
            $event->setType($this->em->getRepository(EventType::class)->findOneByShortName('Experts'));
            $place = $this->em->getRepository(Place::class)->find(17); //cci lille ?
            $event->setPlace($place);

            $event_type = $event->getType();
            foreach ($event_type->getOrganizationTypes() as $type) {
                $event->addOrganizationType($type);
            }

            $event->setManager($this->em->getRepository(User::class)->find(2));
            //$event->setSlug('experts-reims-test');
            $event->setOnline(new \Datetime('2019-01-01 09:00'));
            $event->setDate(new \Datetime('2019-06-06 09:00'));
            $event->setOffline(new \Datetime('2020-06-06 09:00'));
            $event->setClosingAt(new \Datetime('09:00'));
            $event->setNbStand(20);

        }


            //registration
            $section_type = $this->em->getRepository(SectionType::class)->findOneBySlug('registration');
            $section_registration = new SectionSimple();
            $section_registration->setSectionType($section_type);
            $section_registration->setTitle($section_type->getTitle());
            $section_registration->setMenuTitle($section_type->getTitle());
            $section_registration->setSorder($section_type->getSorder());
            $section_registration->setOnPublic(true);
            $section_registration->setEvent($event);
            $this->em->persist($section_registration);

            //sectors
            $section_type = $this->em->getRepository(SectionType::class)->findOneBySlug('sectors');
            $section_sectors = new SectionSector();
            $section_sectors->setSectionType($section_type);
            $section_sectors->setTitle($section_type->getTitle());
            $section_sectors->setMenuTitle($section_type->getTitle());
            $section_sectors->setSorder($section_type->getSorder());
            $section_sectors->setOnPublic(true);
            $section_sectors->setEvent($event);
            $this->em->persist($section_sectors);

            $sector = $this->em->getRepository(Sector::class)->find(1); //Administratif / gestion
            $sector2 = $this->em->getRepository(Sector::class)->find(5); //Banque / finance

            $event->addSector($sector);
            $event->addSector($sector2);

            //concept



        

        // $this->addExposant($this->em,$event);

        $this->em->persist($event);
        $this->em->flush();

    }
}
