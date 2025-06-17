<?php

namespace App\Command;

use App\Entity\EmailType;
use App\Entity\Event;
use App\Helper\MailerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArnaudRecallCommand extends Command
{
    protected static $defaultName = 'arnaud:recall';

    private $em;
    private $mailer;

    public function __construct(MailerHelper $mailer)
    {
        parent::__construct();
        $this->em = $mailer->getEm();       
        $this->mailer = $mailer;
        //$this->helper = $helper;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send recall to arnaud')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email_types = $this->em->getRepository(EmailType::class)->findAll();

        foreach ($email_types as $email_type) {
            $days = $email_type->getDays();

            if ($days){
                $io->text($email_type->getLabel());

                if ($days != -3){
                    $events_recall = $this->em->getRepository(Event::class)->findRecallArnaud($days);
                }
                else{                    
                    $events_recall = $this->em->getRepository(Event::class)->findD3RecallArnaud(new \Datetime());
                    //to test set new \Datetime('2018-08-06'));
                }

                if ($events_recall){
                    foreach ($events_recall as $recall) {
                        $io->text($recall);
                        $this->mailer->sendRecallArnaud($email_type,$recall);
                    }
                }
            }
        }

        $io->success('Arnaud recall ok');
        return 1;
    }
}
