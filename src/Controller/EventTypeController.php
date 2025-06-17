<?php

namespace App\Controller;

use App\Entity\EventType;
use App\Form\EventTypeType;
use App\Repository\EventTypeRepository;
use App\Helper\ImageHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EventType controller.
 * @Route("/admin/event-type")
 */
class EventTypeController extends AbstractController
{
    /**
     * Lists all event_type entities
     * @Route("/", name="event_type_index")
     * @Template()
     */
    public function indexAction(EventTypeRepository $eventTypeRepository): Response
    {
        $event_types = $eventTypeRepository->findAll();

        return $this->render('event_type/index.html.twig', array(
                'event_types' => $event_types)
        );
    }

    /**
     * @Route("/new", name="event_type_new", methods="GET|POST")
     */
    public function new(Request $request, ImageHelper $image_helper): Response
    {
        $event_type = new EventType();

        $form = $this->createForm(EventTypeType::class, $event_type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ok = $image_helper->handleImage($em, $event_type->getHeaderFile(), $form->get('headerFile'), 'headerEventType');
            if ($ok) {
                $em->persist($event_type);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Le type d\'événement a été créé'
                );

                return $this->redirectToRoute('event_type_index');
            }
        }

        return $this->render('event_type/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="event_type_edit", requirements={"id" = "\d+"}, methods="GET|POST")
     */
    public function editAction(Request $request, EventType $event_type, ImageHelper $image_helper)
    {
        $form = $this->createForm(EventTypeType::class, $event_type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ok = $image_helper->handleImage($em, $event_type->getHeaderFile(), $form->get('headerFile'), 'headerEventType');
            if ($ok) {
                $em->persist($event_type);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Le type d\'événement a été modifié'
                );

                return $this->redirectToRoute('event_type_index');
            }
        }

        return $this->render('event_type/edit.html.twig', [
            'event_type' => $event_type,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/delete/{id}", name="event_type_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction(EventType $event_type)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($event_type);
        $em->flush();

        $this->addFlash(
            'success',
            'Le type d\'événement a été supprimé'
        );

        return $this->redirectToRoute('event_type_index');
    }

}
