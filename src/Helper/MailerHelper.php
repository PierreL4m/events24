<?php

namespace App\Helper;
use App\Entity\Accreditation;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\Section;
use App\Entity\Participation;
use App\Entity\ResponsableBis;
use App\Entity\User;
use App\Helper\H48Helper;
use App\Helper\RenderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerHelper
{
    public  $templating;
    private $mailer;
    private $em;
    private $admin_online ;
    private $public_dir;
    private $helper;
    private $snappy;
    private $render_helper;
    private $logger;

    public function __construct(LoggerInterface $logger, Environment $templating, MailerInterface  $mailer, EntityManagerInterface $em, $admin_online, $public_dir, GlobalHelper $helper, Pdf $snappy, RenderHelper $render_helper)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->em = $em;
        $this->admin_online = $admin_online;
        $this->public_dir = $public_dir;
        $this->helper = $helper;
        $this->snappy = $snappy;
        $this->render_helper = $render_helper;
        $this->logger = $logger;
    }
    public function getEm()
    {
        return $this->em;
    }
    /**
     * send an email with the following params
     * @param  array $from              the email sender [$email => $name]
     * @param  string/array $to         the email recipient ['other@domain.org' => 'A name']
     * @param  string $subject          email subject
     * @param  string $template_name    template name 'emails/'.$template_name.'.html.twig'
     * @param  array  $template_params  template params
     * @param  array $ccs               recipients copy
     * @param  array $bccs              recipients hidden copy
     * @param  array $attachement       email attachments (relative path)
     */
    public function sendMail(
        $to,
        $subject,
        $template_name,
        $template_params = array(),
        $from="evenements@l4m.fr",
        $ccs=null,
        $bccs=null,
        $attachment=null,
        $reply_to=null
    )
    {

        if(!is_array($to)) {
            $to = [$to];
        }
        $message = (new Email())
            ->from($from)
            ->to(...$to)
            ->subject($subject)
            ->html(
                $this->templating->render(
                    'emails/'.$template_name.'.html.twig', $template_params
                ),
                'text/html'
            )
        ;

        if($reply_to){
            $message->replyTo($reply_to);
        }

        if ($ccs){
            $i=0;
            foreach ($ccs as $cc) {
                if ($i==0){
                    if(is_array($cc)){
                        $message->cc($cc['email']);
                    }
                    else{
                        $message->cc($cc);
                    }
                    $i++;
                }
                else{
                    if(is_array($cc)){
                        $message->cc($cc['email']);
                    }
                    else{
                        $message->cc($cc);
                    }
                }
            }
        }

        if ($bccs){
            foreach ($bccs as $bcc) {
                $message->bcc($bcc);
            }
        }

        if ($attachment){

            if (is_array($attachment)){
                $error = false;

                foreach ($attachment as $a) {
                    $absolute_path = $this->public_dir.$a;

                    if(file_exists($absolute_path) && is_file($absolute_path)){
                        $message->attachFromPath($this->public_dir.$a);
                    }
                    else{
                        $error = true;
                    }
                }
                // if ($error){
                //     $message_error = clone($message);
                //     $message_error->setTo('webmaster@l4m.fr');
                //     $message_error->setSubject($message->getSubject()." - attachment not found ");
                //     $this->mailer->send($message_error);
                // }
            }
            else{
                $absolute_path = $this->public_dir.$attachment;

                if(file_exists($absolute_path) && is_file($absolute_path)){
                    $message->attachFromPath($this->public_dir.$attachment);
                }
                // else{
                //     $message_error = clone($message);
                //     $message_error->setTo('webmaster@l4m.fr');
                //     $message_error->setSubject("attachment not found ".$this->public_dir.$attachment." - ".$message->getSubject());
                //     $this->mailer->send($message_error);
                // }
            }
        }

        $this->mailer->send($message);
    }

    public function sendPasswordToAdmins(User $user,$password)
    {
        if(get_class($user) == ExposantUser::class){
            $emails = $this->em->getRepository(L4MUser::class)->getAdminEmails();
            $tos = array();

            foreach ($emails as $value) {
                foreach ($value as $key => $val) {
                    if ($key == 'email'){
                        array_push($tos, $val);
                    }
                }
            }

            $this->sendMail(
                $tos,
                'Nouveau mot de passe admin events',
                'password',
                array('user' => $user, 'password' => $password)
            );
        }
    }

    public function sendParticipationMail(User $user, Participation $participation, $password=null, $to_me=null)
    {
        $ccs = $this->em->getRepository(ResponsableBis::class)->getResponsableBis($participation);
        $first = null;
        if ($password == null){
            if($user->getLastLogin() == null || $user->getLastLogin()->format('Y-m-d') < $this->admin_online){
                $first = true;
            }
        }
        $manager = $participation->getEvent()->getManager();
        $to =  $user->getEmail();
        if ($to_me){
            $to=$to_me;
        }
        $this->sendMail(
            $to,
            'Complétez dès maintenant votre fiche exposant pour l\'événement L4M "'.$participation->getEvent()->__toString().'"',
            'edit_participation',
            array('responsable' => $user, 'password' => $password, 'participation'=> $participation, 'first' => $first),
            $manager->getEmail(),
            $ccs
        );

    }

    public function sendOfferMail(User $user, Participation $participation, $password=null, $to_me=null)
    {


        $ccs = $this->em->getRepository(ResponsableBis::class)->getResponsableBis($participation);
        $first = null;
        if ($password == null){
            if($user->getLastLogin() == null || $user->getLastLogin()->format('Y-m-d') < $this->admin_online){
                $first = true;
            }
        }
        $manager = $participation->getEvent()->getManager();

        $to =  $user->getEmail();
        if ($to_me){
            $to=$to_me;
        }
        $this->sendMail(
            $to,
            'Renseignez dès maintenant vos offres pour l\'événement L4M de "'.$participation->getEvent()->__toString().'"',
            'add_offers',
            array('responsable' => $user, 'password' => $password, 'participation'=> $participation, 'first' => $first),
            $manager->getEmail(),
            $ccs
        );

    }

    public function sendRecallCandidate(User $user, CandidateParticipation $candidateParticipation, $password=null, $to_me=null)
    {

        $first = null;
        if ($password == null){
            if($user->getLastLogin() == null || $user->getLastLogin()->format('Y-m-d') < $this->admin_online){
                $first = true;
            }
        }
        $manager = $candidateParticipation->getEvent()->getManager();

        $to =  $user->getEmail();
        if ($to_me){
            $to=$to_me;
        }

        if($candidateParticipation->getSlot() == null){
            $subject = 'Vous n\'êtes pas inscrit à un créneau pour l\'évenement"'.$candidateParticipation->getEvent()->__toString().'"';
        }else {
            $subject = 'Vous participez à l\event "' . $candidateParticipation->getEvent()->__toString() . '"';
        }
        $this->sendMail(
            $to,
            $subject,
            'recall_candidate',
            array('candidat' => $user, 'password' => $password, 'candidateParticipation'=> $candidateParticipation, 'first' => $first),
            $manager->getEmail()
        );
    }

    public function sendNetworkFiles(User $user, Participation $participation, $password=null, $to_me=null)
    {

        $first = null;
        if ($password == null){
            if($user->getLastLogin() == null || $user->getLastLogin()->format('Y-m-d') < $this->admin_online){
                $first = true;
            }
        }
        $manager = $participation->getEvent()->getManager();

        $to =  $user->getEmail();
        if ($to_me){
            $to=$to_me;
        }

        $facebookVisuel = '/uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getFacebookVisuel());
        $twitterVisuel = '/uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getTwitterVisuel());
        $linkedinVisuel = '/uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getLinkedinVisuel());
        $instaVisuel = '/uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getInstaVisuel());
        $signatureVisuel = '/uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getSignatureVisuel());
        $attachements = array($facebookVisuel, $twitterVisuel, $linkedinVisuel,$instaVisuel,$signatureVisuel);
        $dateEvent = date_create(date_format($participation->getEvent()->getDate(), 'Y-m-d '));
        $currentDate= date_create(date('Y-m-d '));
        if(6 >= $dateEvent->diff($currentDate)->days){
            $this->sendMail(
                $to,
                'J-'.$dateEvent->diff($currentDate)->days.' !!! Visuels web - 24 Heures pour l\'Emploi et la Formation - "'.$participation->getEvent()->__toString().'"',
                'visuels_rsRecall',
                array('responsable' => $user, 'password' => $password, 'participation'=> $participation, 'first' => $first),
                $manager->getEmail(),
                null,
                null,
                $attachements
            );
        }else{
            if($participation->getEvent()->getType()->getShortName() == "48"){
                $this->sendMail(
                    $to,
                    'Visuels web - Jobfest - "'.$participation->getEvent()->__toString().'"',
                    'visuels_rs',
                    array('responsable' => $user, 'password' => $password, 'participation'=> $participation, 'first' => $first),
                    $manager->getEmail(),
                    null,
                    null,
                    $attachements
                );

            }else{
                $this->sendMail(
                    $to,
                    'Visuels web - 24 Heures pour l\'Emploi et la Formation - "'.$participation->getEvent()->__toString().'"',
                    'visuels_rs',
                    array('responsable' => $user, 'password' => $password, 'participation'=> $participation, 'first' => $first),
                    $manager->getEmail(),
                    null,
                    null,
                    $attachements
                );
            }
        }
    }

    public function sendNewPassword(User $user, $password, Event $event=null)
    {
        $from = null;
        if ($user instanceof CandidateUser){
            $subject = 'Votre compte a été crée pour assister aux salons '.$event->getFullType() ;
            $from = "evenements@l4m.fr";
        }
        else{
            $subject = 'Votre mot de passe pour le back office événements L4M';
            $from = "evenements@l4m.fr";
        }

        $this->sendMail(
            $user->getEmail(),
            $subject,
            'newPassword',
            array('user' => $user, 'password' => $password, 'event' => $event),
            $from
        );
    }

    public function sendWarningExistingAccount(User $user, L4MUser $manager)
    {
        $this->sendMail(
            $manager->getEmail(),
            'Tu as essayé de créer un nouveau compte pour un utilisateur déjà existant sur l\'admin events',
            'existingAccount',
            array('user' => $user, 'manager' => $manager)
        );
    }

    public function sendRecallArnaud(EmailType $email_type, Event $event)
    {
        $this->sendMail(
            new Address('thibaud.cottin@l4m.fr', 'Thibaud'),
            'RAPPEL : rédiger le mail '.$email_type->getLabel().' pour l\'événement '.$event,
            'recallArnaud',
            array('email_type' => $email_type, 'event' => $event, 'team' => true)
        );
    }

    public function sendFiles(Participation $p, $type,$email = null)
    {
        if ($email){
            $to = $email;
            $ccs = null;
        }
        else{
            $responsable = $p->getResponsable();

            if (!$responsable || $responsable->hasRole('ROLE_SUPER_ADMIN')){
                return;
            }
            $to = new Address($responsable->getEmail(), $responsable->getFirstname().' '.$responsable->getLastname());
            $ccs = $responsable->getEmailBises();
        }

        $event = $p->getEvent();

        switch ($type) {
            case 'Bat':
                $template = 'bat';
                $functionName='setBatSent';
                $subject='Votre BAT pour le guide de l\'événement ';
                $attachments = array();

                foreach ($p->getBats() as $bat) {
                    array_push($attachments, '/'.$bat->getPathSrc());
                }
                if (empty($attachments)){
                    return;
                }
                break;

            case 'Tech':
                $template = 'tech_files';
                $functionName='setTechGuideSent';
                $subject='Informations pratiques - ';
                $spec = '/uploads/spec/'.$event->getSpecificationPath();
                $ack = '/uploads/ack/'.$p->getAckPath();
                $tech_guide = '/uploads/tech_guide/'.$p->getStandType()->getTechGuide()->getPath();
                $attachments = array($spec, $ack, $tech_guide);

                //check if joblink
                if (method_exists($p,'getJoblinkSessions')) {
                    foreach ($p->getJoblinkSessions() as $joblink_session){
                        $joblink_pdf = $joblink_session->getJoblink()->getPdfWebPath();
                        array_push($attachments,$joblink_pdf);
                    }
                }

                break;

            default:
                throw new \Exception('cannot send file with type='.$type);
                break;
        }

        $manager = $event->getManager();

        $this->sendMail(
            $to,
            $subject.$event->getTypeCityAndDate(),
            $template,
            array('p' => $p, 'event' => $event,'user' => $manager),
            $manager->getEmail() ,
            $ccs,
            null,
            $attachments
        );

        if (!$email){
            $p->$functionName(new \DateTime());
            $this->em->persist($p);
        }
    }

    public function sendContactForm($datas,Event $event)
    {
        if($datas['from'] == "Demande d'informations"){
            $mail = $event->getManager()->getEmail();
        }else{
            $mail = "l4m.events.59@gmail.com";
        }
        $this->sendMail(
            $mail,
            $datas['from'].' '.$event->getTypeCityAndDate(),
            'contact',
            array('datas' => $datas, 'event' => $event),
            "evenements@l4m.fr",
            $ccs=null,
            $bccs=null,
            $attachment=null,
            $datas['email'][0]
        );
    }

    /**
     * @deprecated
     * @param unknown $candidate
     * @param Event $event
     * @param unknown $recall
     */
    public function sendInvitationOld($candidate, Event $event = null, $recall = null)
    {
        //old
        if (!$candidate->getInvitationPath()){
            return;
        }

        $invitation_path = $candidate->getInvitationPath();

        $em = $this->em;
        $templating = $this->templating;
        $event = $candidate->getEvent();

        if ($recall){
            $subject = 'Voici de nouveau votre ';
        }
        else{
            $subject = 'Votre ' ;
        }
        $second_date = '';

        //does not work
        //cannot die here
        // if(H48Helper::is48($event)){
        //     die();
        //     $second_event = H48Helper::getSecond48($event);

        //     if($second_event){
        //         $second_date = 'et le '.$second_event->getDate()->format('d/m/Y');
        //     }
        // }
        $subject = $subject.'invitation à l\'événement '.$event->getTypeCityAndDate().$second_date;
        $email = str_replace(" ", "", $candidate->getEmail());

        $this->sendMail(
            $email,
            $subject,
            'invitation',
            array('candidate' => $candidate, 'event' => $event, 'recall' => $recall),
            'noreply-events@l4m.fr',
            null,
            null ,
            $invitation_path
        );
    }

    public function sendInvitation(CandidateParticipation $participation, $recall = null)
    {
        if (!$participation->getInvitationPath()){
            return;
        }

        $invitation_path = $participation->getInvitationPath();

        $em = $this->em;
        $templating = $this->templating;
        $event = $participation->getEvent();
        $candidate = $participation->getCandidate();

        if ($recall){
            $subject = 'Voici de nouveau votre ';
        }
        else{
            $subject = 'Votre ' ;
        }
        $subject = $subject.'invitation à l\'événement '.$event->getTypeCityAndDate();
        $email = str_replace(" ", "", $candidate->getEmail());


//to do check if file
//else set invitation path to null
        $this->sendMail(
            $email,
            $subject,
            'invitation',
            array('candidate' => $candidate, 'event' => $event, 'recall' => $recall, 'participation' => $participation),
            'noreply-events@l4m.fr',
            null,
            null ,
            $invitation_path
        );
    }

    public function sendAccreditation(Accreditation $accreditation, $recall = null)
    {
        if (!$accreditation){
            return;
        }

        $accreditation_path = $accreditation->getAccreditationPath();

        $em = $this->em;
        $templating = $this->templating;
        $event = $accreditation->getEvent();
        $participation = $accreditation->getParticipation();

        if ($recall){
            $subject = 'Voici de nouveau votre ';
        }
        else{
            $subject = 'Votre ' ;
        }
        $subject = $subject.'accreditation à l\'événement '.$event->getTypeCityAndDate();
        $email = str_replace(" ", "", $accreditation->getEmail());


//to do check if file
//else set invitation path to null
        $this->sendMail(
            $email,
            $subject,
            'accreditation',
            array('accreditation' => $accreditation),
            'noreply-events@l4m.fr',
            null,
            null ,
            $accreditation_path
        );
    }

    //to do handle event in params
    public function sendProfile(CandidateParticipationComment $comment, ExposantUser $user)
    {
        $candidate_participation = $comment->getCandidateParticipation();
        $event = $candidate_participation->getEvent();
        $manager = $event->getManager();
        $candidate = $candidate_participation->getCandidate();

        $subject = 'Profil du candidat '.$candidate;
        $email = $user->getEmail();

        $this->sendMail(
            $email,
            $subject,
            'profile',
            array('candidate' => $candidate, 'event' => $event, 'comment' => $comment, 'user' => $manager, 'responsable' => $user ),
            $manager->getEmail(),
            null,
            null ,
            $candidate->getCvPath()
        );
    }

    public function sendChangeStatusMail(CandidateParticipation $participation)
    {
        $status_slug = $participation->getStatus()->getSlug();
        $event_name = $participation->getEvent()->getTypeCityAndDate();
        $invitation = null;
        switch ($status_slug) {
            case 'registered':
            case 'waiting':
            case 'pending':
                return;

            case 'refused_after_call':
            case 'refused':
                $subject = "Votre candidature à l'événement ".$event_name." n'a pas été retenue";
                $template_name = 'refused' ;
                break;

            case 'confirmed' :
                $subject = 'Votre invitation à l\'événement ' .$event_name;
                $template_name = 'invitation' ;
                $this->render_helper->generateInvitation($participation);
                $invitation = $participation->getInvitationPath();
                $this->em->flush();
                break;
            default:
                return;
        }

        $this->sendMail(
            $participation->getCandidate()->getEmail(),
            $subject,
            $template_name,
            ['participation' => $participation, 'status' => $status_slug, 'recall' => false],
            "evenements@l4m.fr",
            null,
            null,
            $invitation
        );
    }

    public function sendRegistrationMail(CandidateParticipation $participation)
    {
        //change lagel in publiccontrollertest
        $this->sendMail(
            $participation->getCandidate()->getEmail(),
            'Votre inscription à l\'événement '.$participation->getEvent()
                ->getFullType().' a bien été prise en compte',
            'registration_success',
            ['participation' => $participation],
            "evenements@l4m.fr"
        );

        //escape game
        // $recruitment_office = null;
        // $recruiter_mails = null;
        // //$recruitment_office = $event->getRecruitmentOffice();
        // if ($recruitment_office){
        //     $recruiter_mails = [];

        //     foreach ($recruitment_office->getRhs() as $rh) {
        //         array_push($recruiter_mails,$rh->getEmail());
        //     }
        // }

        if($participation->getEvent()->getType()->getId() == 2){
            $this->sendMail(
                $participation->getEvent()->getManager()->getEmail(),
                'Inscription à l\'événement '.$participation->getEvent()
                    ->getFullType(),
                'registration_info',
                ['participation' => $participation, 'team' => true]
            // $recruiter_mails
            );
        }

    }
    public function sendFreeSlots(CandidateUser $candidate, Event $event, $host)
    {
        $email=$candidate->getEmail();
        $this->sendMail(
            $email,
            'De nouveaux créneaux pour l\'événement de '.$event->getPlace()->getCity().' sont disponibles',
            'recallFreeSlots',
            ['event' => $event, "host" => $host]
        // $recruiter_mails
        );
    }
    public function sendRecallSubscribe($email, Section $section, $host)
    {


        $this->sendMail(
            $email,
            'Les inscriptions pour l\'événement de '.$section->getEvent()->getPlace()->getCity().' sont ouvertes',
            'recallSubscribe',
            ['host' => $host,'event' => $section->getEvent()]
        // $recruiter_mails
        );

    }
}
