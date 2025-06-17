<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Section;
use App\Form\AgendaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\ImageHelper;

/**
 * @Route("/admin/agenda")
 */
class AgendaController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="agenda_new", methods="GET|POST", requirements={"id" = "\d+"})
     * @param Request $request
     * @param Section $section
     * @param ImageHelper $image_helper
     * @return Response
     */
    public function new(Request $request, Section $section, ImageHelper $image_helper): Response
    {
        $agenda = new Agenda();
        $agenda->setSection($section);
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ok = $image_helper->handleImage($agenda->getLogoFile(), $form->get('logoFile'), 'agenda', 200, 200);

            if ($ok) {
                $em->persist($agenda);
                $em->flush();

                return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
            }
        }

        return $this->render('agenda/new.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="agenda_edit", methods="GET|POST", requirements={"id" = "\d+"})
     * @param Request $request
     * @param Agenda $agenda
     * @param ImageHelper $image_helper
     * @return Response
     */
    public function edit(Request $request, Agenda $agenda, ImageHelper $image_helper): Response
    {
        $form = $this->createForm(AgendaType::class, $agenda);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $image_helper->handleImage($agenda->getLogoFile(), $form->get('logoFile'), 'agenda', 200, 200);

            if ($ok) {
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->redirectToRoute('section_show', ['id' => $agenda->getSection()->getId()]);
        }

        return $this->render('agenda/edit.html.twig', [
            'agenda' => $agenda,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remove/{id}", name="agenda_delete", methods="GET")
     * @param Request $request
     * @param Agenda $agenda
     * @return Response
     */
    public function delete(Request $request, Agenda $agenda): Response
    {
        //$idSection = $agenda->getSection()->getId;
        // if ($this->isCsrfTokenValid('delete'.$agenda->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($agenda);
        $em->flush();

        // }

        return $this->redirectToRoute('section_show', ['id' => $agenda->getSection()->getId()]);
    }
}
