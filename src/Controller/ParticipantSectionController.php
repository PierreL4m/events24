<?php

namespace App\Controller;

use App\Entity\ParticipantSection;
use App\Entity\Section;
use App\Form\ParticipantSectionType;
use App\Repository\ParticipantSectionRepository;
use App\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/participant/section")
 */
class ParticipantSectionController extends AbstractController
{

    /**
     * @Route("/{id}/new", name="participant_new", methods="GET|POST",requirements={"id":"\d+"})
     */
    public function new(Request $request, ImageHelper $image_helper, Section $section): Response
    {
        $participantSection = new ParticipantSection();
        $form = $this->createForm(ParticipantSectionType::class, $participantSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $image_helper->handleImage($participantSection->getLogoFile(), $form->get('logoFile'), 'sections_participants', 200, 200);

            if ($ok) {
                $participantSection->setSection($section);
                $em = $this->getDoctrine()->getManager();
                $em->persist($participantSection);
                $em->flush();

                return $this->redirectToRoute('section_show', array('id' => $section->getId()));
            }
        }

        return $this->render('participant_section/new.html.twig', [
            'participant_section' => $participantSection,
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="participant_edit", methods="GET|POST",requirements={"id":"\d+"})
     */
    public function edit(Request $request, ImageHelper $image_helper, ParticipantSection $participantSection): Response
    {

        $form = $this->createForm(ParticipantSectionType::class, $participantSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $image_helper->handleImage($participantSection->getLogoFile(), $form->get('logoFile'), 'sections_participants', 200, 200);

            if ($ok) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('section_show', array('id' => $participantSection->getSection()->getId()));
            }
        }

        return $this->render('participant_section/edit.html.twig', [
            'participant_section' => $participantSection,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="participant_delete", methods="GET")
     */
    public function delete(Request $request, ParticipantSection $participantSection): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$participantSection->getId(), $request->request->get('_token'))) {
        $id = $participantSection->getSection()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($participantSection);
        $em->flush();
        //  }

        return $this->redirectToRoute('section_show', array('id' => $id));
    }
}
