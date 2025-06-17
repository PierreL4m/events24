<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\MailerHelper;
use App\Helper\GlobalEmHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;
use App\Entity\Candidate;
use App\Entity\CandidateUser;

class ChangeCvCommand extends Command
{
    protected static $defaultName = 'cv:update';

    private $em;
    private $mailer;
    private $helper;

    public function __construct(MailerHelper $mailer, GlobalEmHelper $helper)
    {
        parent::__construct();
        $this->em = $mailer->getEm();       
        $this->mailer = $mailer;
        $this->helper = $helper;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $events = $this->em->getRepository(Event::class)->findRecallArnaud(1);

        /**
         * 
         * @var Event $event
         */
        $event = $events[0];
        $participations = $event->getCandidateParticipations();
        
        if(count($participations) > 0){
            $this->mailer->sendMail(
                'webmaster@l4m.fr',
                'Update cv should have been sent',
                'raw',       
                array('body' => 'check command update cv if img'),        
                array("webmaster@l4m.fr" => "Admin Events")
            );
        }
        else{
            $io->success('no event to sent update your cv');
            return;
        }

        foreach ($participations as $participation) {
            /**
             * 
             * @var CandidateUser $candidate
             */
            $candidate = $participation->getCandidate();
            $cv = $candidate->getCv();

            if(!$cv){
               $this->mailer->sendMail(
                    new Address($candidate->getEmail(), $candidate->getFirstname().' '.$candidate->getLastname()),
                    'Augmenter vos chances d\'être recruté !',
                    'upload_cv',       
                    array('participation' => $participation),
                    array('evenements@l4m.fr' => $participation->getEvent()->getFullType())
                );
            }
            else{
                $images = ['jpg', 'png'] ;                
                $start = strlen($cv) - 3;
                $extension = mb_substr($cv, $start);
                
                if (in_array($extension, $images)){
                    $this->mailer->sendMail(
                        new Address($candidate->getEmail(), $candidate->getFirstname().' '.$candidate->getLastname()),
                        'Augmenter vos chances d\'être recruté !',
                        'upload_cv',       
                        array('participation' => $participation, 'image' => true),
                        array('evenements@l4m.fr' => $participation->getEvent()->getFullType())
                    );
                }
            }  
        }
        
        $io->success('update_cv mails sent');
       
    }

    public function sendRecall($io,$participations,$missing=null)
    {
        foreach ($participations as $p) {
            if ($p->getResponsable() && $p->getResponsable()->getEmail()){
                $event = $p->getEvent();
                $ccs = array();

                foreach ($p->getResponsable()->getResponsableBises() as $bis) {
                    array_push($ccs, $bis->getEmail());
                }

                $this->mailer->sendMail(
                    $p->getResponsable()->getEmail(),
                    'Relance pour votre fiche de participation à l\'événement L4M '.$event->getTypeCityAndDate(),
                    'recall',       
                    array('participation' => $p, 'missing' => $missing),        
                    array($event->getManager()->getEmail() => $event->getManager()),
                    $ccs
                );
                $io->text($p->getCompanyName()." - ".$p->getId());
            }
            else{
                $io->error($p->getCompanyName().' - '.$p->getId().' has no responsable ');
            }
        }
    }
}
