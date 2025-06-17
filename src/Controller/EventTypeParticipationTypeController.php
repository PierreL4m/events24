<?php

namespace App\Controller;

use App\Entity\EventTypeParticipationType;
use App\Form\EventTypeParticipationTypeType;
use App\Repository\EventTypeParticipationTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/event-type-participation-type")
 */
class EventTypeParticipationTypeController extends AbstractController
{
    /**
     * @Route("/", name="event_type_participation_type_index", methods="GET")
     */
    public function index(EventTypeParticipationTypeRepository $eventTypeParticipationTypeRepository): Response
    {
        return $this->render('event_type_participation_type/index.html.twig', ['event_type_participation_types' => $eventTypeParticipationTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="event_type_participation_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $eventTypeParticipationType = new EventTypeParticipationType();
        $form = $this->createForm(EventTypeParticipationTypeType::class, $eventTypeParticipationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventTypeParticipationType);
            $em->flush();

            return $this->redirectToRoute('event_type_participation_type_index');
        }

        return $this->render('event_type_participation_type/new.html.twig', [
            'event_type_participation_type' => $eventTypeParticipationType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_type_participation_type_show", methods="GET")
     */
    public function show(EventTypeParticipationType $eventTypeParticipationType): Response
    {
        return $this->render('event_type_participation_type/show.html.twig', ['event_type_participation_type' => $eventTypeParticipationType]);
    }

    /**
     * @Route("/{id}/edit", name="event_type_participation_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, EventTypeParticipationType $eventTypeParticipationType): Response
    {
        $form = $this->createForm(EventTypeParticipationTypeType::class, $eventTypeParticipationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_type_participation_type_index');
        }

        return $this->render('event_type_participation_type/edit.html.twig', [
            'event_type_participation_type' => $eventTypeParticipationType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_type_participation_type_delete", methods="DELETE")
     */
    public function delete(Request $request, EventTypeParticipationType $eventTypeParticipationType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eventTypeParticipationType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($eventTypeParticipationType);
            $em->flush();
        }

        return $this->redirectToRoute('event_type_participation_type_index');
    }
}
