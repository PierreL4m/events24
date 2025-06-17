<?php

namespace App\Controller;

use App\Entity\Event;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use DoctrineExtensions\Query\Mysql\Year;

class OnSiteRegistrationController extends AbstractController
{
    /**
     * @Route("/onsite/index", name="on_site")
     */
    public function index(EventRepository $er)
    {
        $current_events = $er->findHomePageEvents();
        return $this->render('onsite_registration/index.html.twig', ['current_events' => $current_events]);
    }

    /**
     * @Route("/onsite/checkpoint/{id}", name="on_site_checkpoint")
     */
    public function onsiteCheckpoint(FormHelper $form_helper, Request $request, Event $event, GlobalHelper $helper)
    {
        return $this->render('onsite_registration/checkpoint.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/onsite/registration/{id}", name="on_site_registration")
     */
    public function onsiteRegistration(FormHelper $form_helper, Request $request, Event $event, GlobalHelper $helper, UserPasswordHasherInterface $passwordHasher)
    {
        return $helper->handleResponse($form_helper->registerCandidate($event, $request, 'onsite', $passwordHasher));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('on_site_registration', array('id' => $event->getId()));
        }
        return $this->render('onsite_registration/form.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }
}
