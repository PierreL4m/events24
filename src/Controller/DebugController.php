<?php
/*
{
  "lastname": "string",
  "firstname": "string",
  "email": "stringffvfv@l4m.fr",
  "phone": "0698929728",
  "sectors": [
  ],
  "mobility": "1",
  "degree": "1",
  "city": "1",
  "cv_file": "data:image/png;base64,AAAFBfj42Pj4",
  "plainPassword": "Test1234",
  "mailingEvents": true,
  "mailingRecall": true,
  "phoneRecall": true
}
 */
namespace App\Controller;

use App\Controller\TechFileController;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\Degree;
use App\Entity\Email;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\EventSimple;
use App\Entity\ExposantScanUser;
use App\Entity\Mobility;
use App\Entity\Oauth\Client;
use App\Entity\Organization;
use App\Entity\OrganizationType;
use App\Entity\Participation;
use App\Entity\Place;
use App\Entity\Section;
use App\Entity\User;
use App\Form\Api\RegistrationType;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationRepository;
use App\Repository\EventRepository;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ImageHelper;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Entity\EventType;
use Symfony\Component\Mime\Address;

/**
 * @Route("/admin/debug")
 */
class DebugController extends AbstractController
{

    /**
     * @Route("/registration", name="debug_registration")
     */
    public function debugRegistration(FormHelper $form_helper, Request $request, EventRepository $er)
    {

        $event = $er->find(78);
        return new Response($form_helper->registerCandidate($event, $request, 'web'));
    }



    /*
        {
  "lastname": "string",
  "firstname": "string",
  "email": "string@l4m.fr",
  "plainPassword": {
    "first": "Test1234",
    "second": "Test1234"
  },
  "phone": "0698929728",
  "mailingEvents": true,
  "mailingRecall": true,
  "phoneRecall": true,
  "sectors": [
    "1"
  ],
  "mobility": "1",
  "degree": "1",
  "city": "1"
}
         */


    /**
     * @Route("/", name="debug")
     */
    public function index(LoggerInterface $logger, GlobalHelper $helper, EntityManagerInterface $em, ImageHelper $image_helper, TwigHelper $twig_helper, MailerHelper $mailer_helper, Request $request, EventRepository $er, OrganizationRepository $or, H48Helper $h48_helper)
    {
        //     $organization = $em->getRepository(Organization::class)->findOneByName('test_organization');
        //     dump($organization);die();

        $logger->info('here in debug controller');

        $date = new \Datetime();
        echo($date->format('dmYHi'));
        /*die();
             $event_f = $em->getRepository(Event::class)->find(82);
             $event = $em->getRepository(Event::class)->find(81);

             if ($h48_helper->is48($event) && $h48_helper->getSecond48($event)){
                      $sections = $em->getRepository(Section::class)->findByEvents($event, $h48_helper->getSecond48($event));
                  }
                  else{
                    $sections = $em->getRepository(Section::class)->findByEvent($event->getId());
                  }

                  foreach ($sections as $section) {
                    dump($section->getSectionType()->getMenuTitle());
                    dump($section->getSectionType()->getId());
                  }
                  dump('...');
                  dump($sections);die();*/

        return $this->render('debug/index.html.twig', [
            'controller_name' => 'DebugController',
            'debug' => 'debug'
        ]);
    }

    public function executionSuccess($command, $timeout, $logger)
    {
        $process = new Process($command);

        $now = time();
        $max_time = $now + $timeout;
        $process->start();

        $pid = $process->getPid();
        $logger->info('PID : ' . $pid);
        // exec("cpulimit --pid ".$pid." --limit 30");
        while ($process->isRunning()) {
            if (time() >= $max_time) {
                //$process->signal(9);
                exec('kill -9 ' . $pid + 1);
                $logger->info('kill signal ' . $pid);
                //  $process->setTimeout(1);
                //     throw new ProcessTimedOutException($process,1);
            } else {
                $logger->info('process running');
            }


        }
        if ($process->hasBeenStopped()) {

        }
        $logger->info('not running');

    }

    /**
     * @Route("/invitation/{id}/{mode}", name="debug_invit", requirements={"id": "\d+", "mode": "pdf|html"})
     */
    public function debugInvitation(EntityManagerInterface $em, EventType $event_type, $mode)
    {


        $participation = $em->getRepository(CandidateParticipation::class)->findOneByEventType($event_type);

        $candidate = $participation->getCandidate();
        $qrcode = [
            'lastName' => $candidate->getLastName(),
            'firstName' => $candidate->getFirstName(),
            'userId' => $candidate->getId(),
            'participationId' => $participation->getId()
        ];

        $base64 = base64_encode(json_encode($qrcode));
        $urlQRCode = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=".$base64;
        $participation->setQrCode($urlQRCode);

        $file_name = 'invitation_test.pdf';
        $file_path = $this->get('kernel')->getProjectDir() . '/public/' . $file_name;
        $event = $participation->getEvent();
        //     dump($event->getType()->getShortName());

        //remove invitation
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        //        $snappy = $this->get('knp_snappy.pdf');
        // $snappy->generateFromHtml('http://www.google.com', $file_path);die();
        if ($mode == 'html') {
            return $this->render(
                'snappy/invitation_new.html.twig',
                array(
                    'participation' => $participation
                )
            );
        }
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->generateFromHtml(
            $this->render(
                'snappy/invitation_new.html.twig',
                array(
                    'participation' => $participation
                )
            ),
            $file_path
        );
        return new BinaryFileResponse($file_path);
        dump($snappy->getOutput());
        die();
        return $this->render('snappy/invitation_new.html.twig', [
            'participation' => $participation
        ]);
    }

    //thanks stackoverflow
    public function get_string_between($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * @Route("/send-mail", name="debug_mail")
     */
    public function mail(MailerHelper $mailer_helper, EntityManagerInterface $em, LoggerInterface $logger, TechFileController $tech_controller, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $id = 2973;
        $email = null;
        $type = 'Tech';

        $p = $em->getRepository(Participation::class)->find($id);


        /**
         * 
         * @var User $responsable
         */
        $responsable = $p->getResponsable();

        if (!$responsable || $responsable->hasRole('ROLE_SUPER_ADMIN')) {
            return;
        }
        $to = new Address($responsable->getEmail(), $responsable->getFirstname().' '.$responsable->getLastname());
        $ccs = $responsable->getEmailBises();


        $event = $p->getEvent();


        $template = 'bat';
        $subject = 'Votre BAT pour le guide de l\'événement ';
        $attachments = array();

        foreach ($p->getBats() as $bat) {
            array_push($attachments, $bat->getPathSrc());
        }

        $manager = $event->getManager();

        $this->sendMail(
            $to,
            $subject . $event->getTypeCityAndDate(),
            $template,
            array('p' => $p, 'event' => $event, 'user' => $manager),
            array($manager->getEmail() => $manager->getFullName()),
            $ccs,
            null,
            $attachments,
            $mailer
        );

        return $this->render('debug/index.html.twig', [
            'controller_name' => 'DebugController',
            'participation' => $participation
        ]);
    }

    public function sendMail(
        $to,
        $subject,
        $template_name,
        $template_params = array(),
        $from = array("evenements@l4m.fr" => "Back office événements L4M"),
        $ccs = null,
        $bccs = null,
        $attachment = null,
        $mailer
    )
    {
        $message = $mailer->createMessage()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody(
                $this->render(
                    'emails/' . $template_name . '.html.twig', $template_params
                ),
                'text/html'
            );

        if ($ccs) {
            $i = 0;
            foreach ($ccs as $cc) {
                if ($i == 0) {
                    $message->setCc($cc);
                    $i++;
                } else {
                    $message->addCc($cc);
                }
            }
        }

        if ($bccs) {
            foreach ($bccs as $bcc) {
                $message->addBcc($bcc);
            }
        }
        $public_dir = $this->getParameter('public_dir');
        if ($attachment) {

            if (is_array($attachment)) {
                foreach ($attachment as $a) {
                    $message->attach(\Swift_Attachment::fromPath($public_dir . $a));
                    dump($public_dir . $a);
                }
            } else {
                $message->attach(\Swift_Attachment::fromPath($public_dir . $attachment));
            }
        }

        $mailer->send($message);
    }

    /**
     * @Route("/invitation-experts", name="debug_invitation")
     */
    public function debugInvitationExperts()
    {
        $candidate = new CandidateUser();
        $candidate->setFirstName("France");
        $candidate->setLastname("Benoit");
        $candidate->setEmail('webmaster@l4m.fr');
        $candidate->setEvent($this->getDoctrine()->getManager()->getRepository(Event::class)->find(94));
        $candidate->setQrCode('https://app.eventmaker.io/en/public/events/5a7ab5e831139e001e00129c/guests/5a8b010c1d7ef60022006214/qrcode?secret=37ac0215e6bd54a557cdd02c608a213f64e0c1de');

        return $this->render('public/invitations/invitation_experts.html.twig', [
            'controller_name' => 'DebugController',
            'candidate' => $candidate
        ]);
    }

    /**
     * @Route("/email", name="debug_email")
     */
    public function debugEmail(MailerHelper $mailer)
    {
        $mailer->sendMail('webmaster@l4m.fr', 'test send email', 'test');

        return $this->render('debug/index.html.twig', [
            'controller_name' => 'DebugController',
        ]);
    }

    /**
     * @Route("/signature", name="debug_Signature")
     */
    public function debugSignature()
    {


        return $this->render('emails/test.html.twig', [
            'controller_name' => 'DebugController',
        ]);
    }

    /**
     * @Route("/recall-arnaud", name="debug_recall")
     */
    public function recallArnaud(MailerHelper $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $email_types = $em->getRepository(EmailType::class)->findAll();

        foreach ($email_types as $email_type) {
            $days = $email_type->getDays();
            if ($days) {
                dump($email_type->getLabel());

                if ($days != -3) {
                    $events_recall = $em->getRepository(Event::class)->findRecallArnaud($days);
                } else {

                }
                dump($events_recall);
                if ($events_recall) {
                    foreach ($events_recall as $recall) {
                        dump($recall);
                        $mailer->sendRecallArnaud($email_type, $recall);
                    }
                }
            }
        }
        return $this->render('emails/test.html.twig', [
            'controller_name' => 'DebugController',
        ]);
    }
}
