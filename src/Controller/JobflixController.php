<?php


namespace App\Controller;


use App\Repository\EventRepository;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Repository\ClientRepository;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ParticipationRepository;
use App\Entity\Event;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Repository\EventTypeRepository;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Cookie;

class JobflixController extends AbstractController
{
    /**
     * @Route("/{slug}", name="public_event", methods={"GET|POST"})
     */
    public function event(Event $event, EventRepository $eventRepository)
    {
        $event = $eventRepository->findEventBySlug($event->getSlug());
        return $this->render('jobflix/index.html.twig', array('event' => $event));
    }

    /**
     * @Route("/bilan/{slug}", name="public_bilan", methods={"GET|POST"})
     */
    public function bilan(Event $event, EventRepository $eventRepository)
    {
        $event = $eventRepository->findEventBySlug($event->getSlug());
        return $this->render('jobflix/bilan.html.twig', array('event' => $event));
    }

    /**
     * @Route("/candidat/home", name="candidat_home", methods={"GET|POST"})
     */
    public function homeCandidat()
    {
        return $this->render('jobflix/profil.html.twig');
    }

    /**
     * @Route("/candidat/events", name="candidat_events", methods={"GET|POST"})
     */
    public function events()
    {
        return $this->render('jobflix/profil.html.twig');
    }

    /**
     * @Route("/candidat/profile", name="candidat_profile", methods={"GET|POST"})
     */
    public function profil()
    {
        return $this->render('jobflix/profil.html.twig');
    }

    /**
     * @Route("/scan/home", name="scan_home", methods={"GET|POST"})
     */
    public function scan()
    {
        return $this->render('jobflix/scan.html.twig');
    }



    /*
    public function login()
    {
        return $this->render('jobflix/login.html.twig');
    }*/

    /**
     * @Route("/", name="public_index")
     */
    public function index(SessionInterface $session, EntityManagerInterface $em, FormHelper $form_helper, Request $request, GlobalHelper $helper, H48Helper $helper_48, EventTypeRepository $eventTypeRepository, EventRepository $eventRepository, CityRepository $CityRepository): Response
    {
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
        return $this->render('jobflix/index.html.twig', array('events' => $events));
    }
    /**
     * @Route("/acceuil/concept", name="public_concept")
     */
    public function concept(SessionInterface $session, EntityManagerInterface $em, FormHelper $form_helper, Request $request, GlobalHelper $helper, H48Helper $helper_48, EventTypeRepository $eventTypeRepository, EventRepository $eventRepository, CityRepository $CityRepository): Response
    {
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
        return $this->render('jobflix/index.html.twig', array('events' => $events));
    }
}