<?php

namespace App\Command;


use App\Entity\CandidateUser;
use App\Helper\GlobalHelper;
use App\Helper\TwigHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCvCommand extends Command
{
    protected static $defaultName = 'fix:cv_name';

    private $em;
    private $helper;
    private $twig_helper;
    private $public_dir;

    public function __construct(EntityManagerInterface $em, GlobalHelper $helper, TwigHelper $twig_helper, $public_dir)
    {
        parent::__construct();
        $this->em = $em;  
        $this->helper = $helper;     
        $this->public_dir = $public_dir;
        $this->twig_helper = $twig_helper;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $candidates = $this->em->getRepository(CandidateUser::class)->findAll();

        foreach ($candidates as $candidate) {

            if($candidate->getCv() && $this->twig_helper->fileExists($candidate->getCvPath())){
                $cv_name = $candidate->getCv();
                $pos = strrpos($cv_name, '.');
                $extension = substr($cv_name, $pos);

                $new_name = $this->helper->generateSlug($candidate).uniqid().$extension;
                $cv_dir = $this->public_dir.'/uploads/cvs/';
                rename ($cv_dir.$candidate->getCv(), $cv_dir.$new_name);
                // rename ($cv_dir.$new_name,$cv_dir.$candidate->getCv() );
                $candidate->setCv($new_name);
                
                $io->text($candidate);
            }
        }
        $this->em->flush();

        $io->success('Cv names fixed');
    }
}
