<?php

namespace App\Helper;

use App\Entity\Accreditation;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\EventType;
use App\Entity\Section;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Twig\Environment;

class RenderHelper
{
    private     $project_dir;
    private     $snappy;
    private     $templating;
    private     $helper;
    private     $twig_helper;
    private     $em; 

    public function __construct($project_dir, Pdf $snappy, Environment $templating, GlobalHelper $helper, TwigHelper $twig_helper, EntityManagerInterface $em)
    {
        $this->project_dir = $project_dir;
        $this->snappy = $snappy;
        $this->templating = $templating;
        $this->helper = $helper;
        $this->twig_helper = $twig_helper;
        $this->em = $em;
    }

    public function getTemplating()
    {
        return $this->templating;
    }
    public function getTwigHelper()
    {
        return $this->twig_helper;
    }
    public function getGlobalHelper()
    {
        return $this->helper;
    }

    /**
     * 
     * should not be used anymore
     * @deprecated
     * @param Candidate $candidate
     */
 	public function generateInvitationOld(Candidate $candidate)
    {
        $file_name = 'invitation_'.$this->helper->generateSlug($candidate->getFirstName().'_'.$candidate->getLastName()).uniqid().'.pdf';
        $file_path = $this->project_dir.'/public/invitations/'.$file_name ;
        $event = $candidate->getEvent();
        
        $this->snappy->generateFromHtml(
            $this->templating->render(
                'snappy/invitation'.$event->getType().'.html.twig',
                array(
                    'candidate' => $candidate,
                    'event' => $event, 
                )
            ),
            $file_path
        );

       if ($this->twig_helper->fileExists('/invitations/'.$file_name)){
            $candidate->setInvitationPath($file_name);
        }
    }

    public function generateAccreditation(Accreditation $accreditation)
    {
        if(!$accreditation->getQrCode()){
            $this->helper->generateQrCodeAccred($accreditation);
        }
        $file_name = 'invitation_'.$this->helper->generateSlug($accreditation->getFirstName().'_'.$accreditation->getLastName()).uniqid().'.pdf';
        $file_path = $this->project_dir.'/public/accreditations/'.$file_name ;

        $event = $accreditation->getEvent();
        $text = $this->em->getRepository(Section::class)->findAccredByEvent($accreditation->getEvent()->getId())->getDescription();
        try{
            $this->snappy->generateFromHtml(
                $this->templating->render(
                    'snappy/accreditation_new.html.twig',
                    array(
                        'participation' => $accreditation,
                        'text' => $text,
                        'event' => $event,
                        'registration_joblinks' => $event->getType()->getRegistrationJoblinks() == EventType::REGISTRATION_USE_JOBLINKS
                    )
                ),
                $file_path
            );
        }
        catch(\Exception $e){
            throw $e; //in case invitation generation fail, keep sending invitation in registration action
            $participation->setInvitationPath('error');
        }


        if ($this->twig_helper->fileExists('/accreditations/'.$file_name)){
            $accreditation->setAccreditationPath($file_name);
        }
        $this->em->flush(); // duplication in some controller
        // i wanted to avoid multiple flush  (here and next in controller)
        //so i didnt put it here first
        //but this make sure invitation path is saved
    }

    public function generateAllInvitation(CandidateParticipationRepository $cr)
    {
        if(!$participation->getQrCode()){
            $this->helper->generateQrCode($participation);
        }

        $candidate = $participation->getCandidate();
        $file_name = 'invitation_'.$this->helper->generateSlug($candidate->getFirstName().'_'.$candidate->getLastName()).uniqid().'.pdf';
        $file_path = $this->project_dir.'/public/invitations/'.$file_name ;

        $event = $participation->getEvent();

        try{
            $this->snappy->generateFromHtml(
                $this->templating->render(
                    'snappy/invitation_new.html.twig',
                    array(
                        'participation' => $participation,
                        'event' => $event,
                        'registration_joblinks' => $event->getType()->getRegistrationJoblinks() == EventType::REGISTRATION_USE_JOBLINKS
                    )
                ),
                $file_path
            );
        }
        catch(\Exception $e){
            throw $e; //in case invitation generation fail, keep sending invitation in registration action
            $participation->setInvitationPath('error');
        }


        if ($this->twig_helper->fileExists('/invitations/'.$file_name)){
            $participation->setInvitationPath($file_name);
        }
        $this->em->flush(); // duplication in some controller
        // i wanted to avoid multiple flush  (here and next in controller)
        //so i didnt put it here first
        //but this make sure invitation path is saved
    }
    public function generateInvitation(CandidateParticipation $participation)
    {
        if(!$participation->getQrCode()){           
            $this->helper->generateQrCode($participation);
        }

        $candidate = $participation->getCandidate();
        $file_name = 'invitation_'.$this->helper->generateSlug($candidate->getFirstName().'_'.$candidate->getLastName()).uniqid().'.pdf';
        $file_path = $this->project_dir.'/public/invitations/'.$file_name ;

        $event = $participation->getEvent();

        try{
            $this->snappy->generateFromHtml(
                $this->templating->render(
                    'snappy/invitation_new.html.twig',
                    array(
                        'participation' => $participation,
                        'event' => $event
                    )
                ),
                $file_path
            );
        }
        catch(\Exception $e){
            throw $e; //in case invitation generation fail, keep sending invitation in registration action
            $participation->setInvitationPath('error');
        }


       if ($this->twig_helper->fileExists('/invitations/'.$file_name)){
            $participation->setInvitationPath($file_name);
        }
       $this->em->flush(); // duplication in some controller
        // i wanted to avoid multiple flush  (here and next in controller)
        //so i didnt put it here first
        //but this make sure invitation path is saved
    }

    public function fileExistsOld(Candidate $candidate)
    {
        $invitation_path = $candidate->getInvitationPath();
        $path = $this->project_dir.'/public'.$invitation_path;

        if(file_exists($path) && is_file($path)){
           return true;
        }
        return false;
    }

    public function fileExists(CandidateParticipation $participation)
    {
        $invitation_path = $participation->getInvitationPath();
        $path = $this->project_dir.'/public'.$invitation_path;

        if(file_exists($path) && is_file($path)){
           return true;
        }
        return false;
    }
}