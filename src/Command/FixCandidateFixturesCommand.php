<?php

namespace App\Command;

use App\Entity\CandidateParticipation;
use App\Entity\ExposantUser;
use App\Entity\Status;
use App\Helper\GlobalHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\RhUser;

class FixCandidateFixturesCommand extends Command
{
    protected static $defaultName = 'fix:candidate-fixtures';

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
            ->setDescription('fix candidate-fixtures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $candidate_participations = $this->em->getRepository(CandidateParticipation::class)->findAll();
        $registered = $this->em->getRepository(Status::class)->findOneBySlug('registered');

        foreach ($candidate_participations as $p) {
            if (!$p->getStatus()){
                $p->setStatus($registered);
            }
            if (!$p->getStatusDate()){
                $p->setStatusDate(new \DateTime());
            }
            if (!$p->getInvitationPath()){
                $p->setInvitationPath('fixtures');
            }
            if ($p->getStatus()->getSlug() == 'confirmed' && !$p->getQrCode()){

                $this->helper->generateQrCode($p);
            }
        }
        $this->em->flush();



    }
}
