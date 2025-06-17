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

class EventRecallCommand extends Command
{
    protected static $defaultName = 'event:recall';
    
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
    
    
    protected function configure()
    {
        $this
        ->setDescription('Send recall mails to exposant')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $events = $this->em->getRepository(Event::class)->findRecall();

        if(count($events) > 0){
            $this->mailer->sendMail(
                'webmaster@l4m.fr',
                'Recall should have been sent',
                'recall_event_list',
                array('events' => $events),
                'webmaster@l4m.fr'
                );
        }
        else{
            $io->success('no recall mail to sent');
            return;
        }
        
        foreach ($events as $event) {
            $io->title($event);
            $not_logged = $this->em->getRepository(Participation::class)->getNotLogged($event);
            $missingdatas = $this->em->getRepository(Participation::class)->getMissingDatas($event);
            $io->comment($not_logged);
            $this->sendRecall($io,$not_logged);
            $this->mailer->sendMail(
                $event->getManager()->getEmail(),
                'Liste des exposants relancés pour l\'événement '.$event,
                'recall_list',
                array('not_logged' => $not_logged),
                'webmaster@l4m.fr'
                );
            $io->success('recall mails sent');
        }
        
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
                    $event->getManager()->getEmail(),
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