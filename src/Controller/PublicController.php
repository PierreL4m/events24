<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Event;
use App\Entity\Section;
use App\Entity\User;
use App\Entity\Slots;
use App\Entity\Job;
use App\Entity\RecallSubscribe;
use App\Form\ContactType;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\EventRepository;
use App\Repository\CityRepository;
use App\Repository\ParticipationRepository;
use App\Repository\JobRepository;
use App\Entity\Participation;
use App\Entity\RhUser;
use App\Entity\ClientUser;
use App\Entity\L4MUser;
use App\Entity\CandidateUser;
use App\Repository\CandidateUserRepository;
use App\Repository\EventTypeRepository;
use App\Entity\ResponsableBis;
use Doctrine\ORM\EntityManagerInterface;

class PublicController extends AbstractController
{

    /**
     * @Route("/showJobContent/{id}", name="show_job_content", methods="GET", requirements={"id" = "\d+"})
     */
    public function showJobContent(Request $request, Job $job)
    {
        return $this->render('partials/job_public.html.twig', array('job' => $job));
    }

    /**
     * @Route("/divParticipant/{id}/{index}_{max}", name="div_participant", methods="GET", requirements={"id" = "\d+"})
     */
    public function idParticipant($id, $index, $max, TwigHelper $helper, Participation $participation, Request $request, JobRepository $jr)
    {
        $finalArray = explode(",", $_GET['data']);
        $jobs = $jr->findByParticipation($participation);
        return $this->render('partials/participation_public.html.twig', array('participation' => $participation, 'index' => $index, 'max' => $max, 'array' => $finalArray, 'jobs' => $jobs));
    }

    /**
     * @Route("/nextModal/{id}/{index}_{max}", name="next_div", methods="GET", requirements={"id" = "\d+"})
     */
    public function nextModal($id, $index, $max, TwigHelper $helper, Participation $participation, JobRepository $jr)
    {
        $finalArray = explode(",", $_GET['data']);
        $jobs = $jr->findByParticipation($participation);
        return $this->render('partials/participation_public.html.twig', array('participation' => $participation, 'index' => $index, 'max' => $max, 'array' => $finalArray, 'jobs' => $jobs));
    }

    /**
     * @Route("/prevModal/{id}/{index}_{max}", name="prev_div", methods="GET", requirements={"id" = "\d+"})
     */
    public function prevModal($id, $index, $max, TwigHelper $helper, Participation $participation, JobRepository $jr)
    {
        $finalArray = explode(",", $_GET['data']);
        $jobs = $jr->findByParticipation($participation);
        return $this->render('partials/participation_public.html.twig', array('participation' => $participation, 'index' => $index, 'max' => $max, 'array' => $finalArray, 'jobs' => $jobs));
    }

    /**
     * @Route("/", name="public_index")
     */
    public function index(FormHelper $form_helper, Request $request, GlobalHelper $helper, H48Helper $helper_48, EventTypeRepository $eventTypeRepository, EventRepository $eventRepository, CityRepository $CityRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        //Get the host according to the address entered in the url
        //$host = $request->getHost();
        $host = $request->getHost();
        try {
            if (!empty($map = $this->getParameter('host_map'))) {
                $host = $helper->mapInputHost($host, $map);
            }
        } catch (\Exception $e) {

        }

        $events = $em->getRepository(Event::class)->findHomePageEvents(($host == $this->getParameter('main_host') ? null : $host));
        $nb = count($events);
        if ($nb == 0) {
            // $host = $request->getHost();
            $eventType = $eventTypeRepository->eventTypeByHost($host)->getId();
            $lastEvent = $eventRepository->findLastByType($eventType);
            $idParticipant = 15;
            return $helper->handleResponse($form_helper->waitingEvent($lastEvent[0], $request, 'web', $CityRepository, $idParticipant));
        } elseif ($nb == 1) {
            //Directly displays the event page
            return $this->redirectToRoute('public_event', array('slug' => $events[0]->getSlug()));
        } elseif ($nb == 2 && $helper_48->is48($events[0]) && $helper_48->is48($events[1])) {
            $e = $helper_48->getMain48($events[0]);
            $e2 = $helper_48->getMain48($events[1]);

            if ($e->getId() && $e2->getId()) {
                return $this->redirectToRoute('public_event', array('slug' => $e->getSlug()));
            }
        }
        if($host == "recrutementexperts.fr" || $host == "www.recrutementexperts.fr"){
            $nextExpert = $em->getRepository(Event::class)->findNextExpert();
            return $this->redirectToRoute('public_event', array('slug' => $nextExpert->getSlug()));

        }
        return $this->render('public/index.html.twig', array('events' => $events));
    }

    /**
     * @Route("/admin-recruteurs", name="recruitement_index")
     */
    public function recruitment(EventRepository $er, Request $request): Response
    {
        if (!($this->getUser() instanceof RhUser)) {
            // this shoud not happen
            return $this->redirectToRoute('public_index');
        }

        $ro = $this->getUser()->getRecruitmentOffice();
        $paginator = $this->get('knp_paginator');
        $events = $paginator->paginate(
            $er->findCurrentRecruitmentEvents($ro),
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );
        return $this->render('recruitment/index.html.twig', [
            'events' => $events,
            'form' => $form
        ]);
    }

    /**
     * @Route("/check-captcha", name="check_captcha", methods="POST")
     */
    public function checkCaptcha(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $token = $request->request->get('token');
            $ip = $request->getClientIp();
            if ($this->get('kernel')->getEnvironment() == 'prod') {
                $secret = "6LdpB3AUAAAAAGzOEO19FEN3sQRfvFp1OkfJ1yPR";
            } else {
                $secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
            }
            // Get cURL resource
            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => array(
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip
                )
            ));
            // Send the request & save response to $resp
            $resp = curl_exec($curl);
            $readable = json_decode($resp);

            $code = $readable->success ? 200 : 401;

            // Close request to clear up some resources
            //curl_close($curl);

            return new JsonResponse(array($resp), $code);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/check-email", name="check_email", methods="POST")
     */
    public function checkEmail(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $email = $request->request->get('email');
            $exists = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneByEmail($email);

            if ($exists) {
                $result = ['exists' => true];
            } else {
                $result = ['exists' => false];
            }

            if (($u = $this->getUser()) && (
                    $u instanceof ClientUser ||
                    $u instanceof RhUser ||
                    $u instanceof L4MUser
                )) {
                $result['message'] = 'adm';
            } else {
                $result['message'] = 'candidate';
            }

            return new JsonResponse($result, 200);

            // if ($exists){
            //   return new JsonResponse([],400);
            // }
            // else{
            //   return new JsonResponse([],200);
            // }
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/sibhook", name="public_sendinblue_webhook")
     */
    public function sendinblue(Request $request, ValidatorInterface $validator, CandidateUserRepository $repo)
    {
        if ($request->get('id') != 146504 || !$request->get('email')) {
            return new Response('');
        }
        $email = $request->get('email');

        $emailConstraint = new Assert\Email();
        $errors = $validator->validate(
            $email,
            $emailConstraint
        );

        if (count($errors)) {
            return new Response('');
        }

        $candidates = $repo->findByEmail($email);

        if (count($candidates) <= 0) {
            return new Response('cf');
        }
        $unvalidate_email = false;
        switch ($request->get('event')) {
            case 'hard_bounce':
            case 'spam':
                $unvalidate_email = true;
            default:
                break;
        }

        $tag = $request->get('tag');
        $remove_recall = $remove_events = false;
        if (empty($tag) || $unvalidate_email) {
            // desinscription newsletter ET mailing
            $remove_recall = $remove_events = true;
        } else {
            if (false !== strpos($tag, 'newsletter')) {
                $remove_recall = true;
            }
            if (false !== strpos($tag, 'mailing')) {
                $remove_events = true;
            }
        }

        foreach ($candidates as $candidate) {
            if ($remove_events) {
                $candidate->setMailingEvents(false);
            }
            if ($remove_recall) {
                $candidate->setMailingRecall(false);
            }
            if ($unvalidate_email) {
                // TODO
            }
        }
        $this->getDoctrine()->getManager()->flush();

        return new Response('1');
    }


//    /**
//     * @Route("/{slug}", name="public_event", methods={"GET|POST"})
//     */
//    public function event(FormHelper $form_helper, Request $request, Event $event, GlobalHelper $helper, H48Helper $h48_helper, CityRepository $CityRepository, UserPasswordHasherInterface $passwordHasher): Response
//    {
//        $idParticipant = 15;
//        return $helper->handleResponse($form_helper->registerCandidate($event, $request, 'web',$passwordHasher, $CityRepository, $idParticipant));
//    }

    /**
     * @Route("/kit-presse/{slug}", name="public_press")
     */
    public function downloadKit(Event $event)
    {
        $response = $this->forward('App\Controller\PressFileController::downloadKit', array(
            'id' => $event->getId()
        ));

        return $response;
    }

    /**
     * @Route("/desinscription/{email}", name="public_unsuscribe_old")
     */
    public function unsubscribe($email, ValidatorInterface $validator): Response
    {
        return $this->redirectToRoute('public_unsuscribe', array('type' => 'evenements', 'email' => $email));
    }

    /**
     * @Route("/desinscription/{type}/{email}", name="public_unsuscribe")
     */
    public function unsuscribeRecall($type, $email, ValidatorInterface $validator): Response
    {

        $method = null;
        switch ($type) {
            case 'evenements':
                $method = 'setMailingEvents';
                break;
            case 'info':
                $method = 'setMailingRecall';
                break;
            // TODO case 'all' : $method = 'setMailingAll'; break;
            default :
                return $this->redirectToRoute('public_index');
        }

        $emailConstraint = new Assert\Email();
        $errors = $validator->validate(
            $email,
            $emailConstraint
        );

        if (0 === count($errors)) {
            $em = $this->getDoctrine()->getManager();
            $success = false;
            $candidates = $em->getRepository(CandidateUser::class)->findByEmail($email);
            if (count($candidates) > 0) {
                foreach ($candidates as $candidate) {
                    $candidate->$method(false);
                }
                $this->getDoctrine()->getManager()->flush();
                $success = true;
            } else {
                $path = $this->get('kernel')->getProjectDir() . '/var/unsubscribe.txt';
                if (false !== ($fp = fopen($path, 'a+b')) &&
                    false !== fputs($fp, $email . "\n")
                ) {
                    fclose($fp);
                    $success = true;
                }
            }

            return $this->render('public/unsubscribe.html.twig', array(
                'candidate' => $candidates,
                'email' => $email,
                'success' => $success,
                'type' => $type
            ));
        } else {
            return $this->redirectToRoute('public_index');
        }
    }

    /**
     * @Route("/{city}/{year}", name="redirect", requirements={"year" = "\d+","city"="lomme|valenciennes|dunkerque|rouen|le_havre|amiens|arras_st_laurent_blangy|boulogne_sur_mer|compiegne|rennes|evreux"})
     */
    public function redirectAction(Request $request, $city, $year)
    {
        $http = $request->getScheme() . '://';
        $host = $request->getHost();

        $old = ['boulogne_sur_mer',
            'valenciennes',
            'dunkerque',
            'arras_st_laurent_blangy'
        ];

        if ($year > 2016 && !in_array($city, $old)) {
            $slug = str_replace('_', '-', $city) . '-' . $year;

            $url = $http . $host . '/' . $slug;
        } else {

            $url = $http . $host;

        }
        return $this->redirect($url, 301);
    }

    /**
     * @Route("/{city}/{year}/{slug}", name="redirect_old", requirements={"year" = "\d+","city"="lomme|valenciennes|dunkerque|rouen|le_havre|amiens|arras_st_laurent_blangy|boulogne_sur_mer|compiegne|rennes|evreux"})
     */
    public function redirectOldAction(Request $request, $city, $year, $slug)
    {
        $http = $request->getScheme() . '://';
        $host = $request->getHost();

        $old = ['boulogne_sur_mer',
            'valenciennes',
            'dunkerque',
            'arras_st_laurent_blangy'
        ];

        if ($year > 2016 && !in_array($city, $old)) {
            $slug = str_replace('_', '-', $city) . '-' . $year;

            $url = $http . $host . '/' . $slug;
        } else {

            $url = $http . $host;

        }
        return $this->redirect($url, 301);
    }

    /**
     * for estuaire emploi
     * @Route("/estuaire-emploi/desinscription/{email}")
     */
    public function unsubscribeAction($email)
    {
        $file = $this->getParameter('public_dir') . '/unsubscribe.txt';
        $file = fopen($file, "a");
        fwrite($file, $email . ";");
        fclose($file);

        return $this->render('public/estuaire_unsuscribe.html.twig', array(
                'email' => $email
            )
        );
    }


    /**
     * @Route("/recall_register/{id}", name="recall_register", methods="POST|GET")
     */
    public function RecallRegister(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $host = $request->getHost();
        if (!empty($request->get('mail'))) {
            $class = '\App\Entity\\RecallSubscribe';
            $recall = new $class;
            $recall->setEmail($request->get('mail'));
            $recall->setEvent($event);
            $em->persist($recall);
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Enregistrement validé, vous serez alerté(e) à l\'ouverture des inscriptions');
        }
        $events = $em->getRepository(Event::class)->findHomePageEvents($host);
        return $this->redirectToRoute('public_event', array('slug' => $event->getSlug()));
    }
}
