<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Event;
use App\Entity\Section;
use App\Form\ContactType;
use App\Form\Api\RegistrationType;
use App\Form\SectionAddType;
use App\Form\SectionRemoveType;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use App\Repository\RecallSubscribeRepository;
use App\Helper\ImageHelper;
use App\Helper\MailerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CandidateUser;

/**
 * @Route("/admin/section")
 */
class SectionController extends AbstractController
{
    /**
     * @Route("/reorderSection/{event}/{id}_{sectionPosition}", name="reorder_section", methods="POST", requirements={"id"="\d+"})
     */
    public function reorderSection($id, $sectionPosition, Request $request, Event $event, Section $section)
    {
        $em = $this->getDoctrine()->getManager();
        $section->setEvent($event);
        $section->setsOrder($sectionPosition);
        $date = new \DateTime();
        $section->setLastUpdate($date);
        $em->persist($section);
        $em->persist($event);
        $em->flush();
        $this->addFlash('success', 'Les rubriques ont été ajoutées');
        return $this->render('section/index.html.twig', [
            'event' => $event,
            'sections' => $section
        ]);
    }

    /**
     * @Route("/{id}", name="section_index", requirements={"id"="\d+"}, methods="GET")
     */
    public function index(SectionRepository $sectionRepository, Event $event): Response
    {
        return $this->render('section/index.html.twig', ['sections' => $sectionRepository->findByEvent($event->getId()), 'event' => $event]);
    }

    /**
     * @Route("/add/{id}", name="section_add", requirements={"id"="\d+"} , methods="GET|POST")
     */
    public function add(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectionAddType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $section_types = $form->get('section_types')->getData();

            foreach ($section_types as $section_type) {
                $class = '\App\Entity\\' . $section_type->getSectionClass();
                $section = new $class;
                $section->setSectionType($section_type);
                $section->setSectionTypeData();
                $section->setEvent($event);
                $section->setsOrder(0);
                $em->persist($section);
            }

            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Les rubriques ont été ajoutées');

            return $this->redirectToRoute('section_index', ['id' => $event->getId()]);
        }

        return $this->render('section/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/remove/{id}", name="section_remove", requirements={"id"="\d+"} , methods="GET|POST")
     */
    public function remove(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectionRemoveType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sections_to_remove = $form->get('sections')->getData();

            foreach ($sections_to_remove as $section) {

                $event->removeSection($section);
                $em->remove($section);
            }

            $em->persist($event);
            $em->flush();


            $this->addFlash('success', 'Les rubriques ont été supprimées');

            return $this->redirectToRoute('section_index', ['id' => $event->getId()]);
        }

        return $this->render('section/remove.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="section_show", methods="GET")
     */
    public function show(Section $section): Response
    {
        $candidate = new CandidateUser();
        $form = $this->createForm(RegistrationType::class, $candidate);
        $contact_form = $this->createForm(ContactType::class);

        return $this->render(
            'section/show.html.twig', [
                'section' => $section,
                'form' => $form->createView(),
                'contact_form' => $contact_form->createView()
            ]
        );
    }


    /**
     * @Route("/{id}/edit", name="section_edit", methods="GET|POST")
     */
    public function edit(Request $request, Section $section, ImageHelper $image_helper): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //check if no image format error and set up width height uplaod folder
            $ok = $image_helper->handleImage($section->getImage(), $form->get('imageFile'), 'sections', 940, 650);

            if ($ok) {
                if ($form->get('remove_img')->getData()) {
                    $image = $section->getImage();
                    $section->setImage(null);
                    $em->remove($image);
                }

                $em->persist($section);
                $em->flush();

                return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
            }
        }

        return $this->render('section/edit.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/subscribePublic/{id}", name="subscribePublic", methods="GET|POST")
     */
    public function subscribePublic(Request $request, Section $section, MailerHelper $mailerHelper, RecallSubscribeRepository $recallSubscribeRepo): Response
    {
        $host = $request->getHost();
        $mails = $recallSubscribeRepo->findByEvent($section->getEvent());
        $em = $this->getDoctrine()->getManager();
        $section->setOnPublic(1);
        $em->persist($section);
        $em->flush();
        foreach ($mails as $user) {
            $mailerHelper->sendRecallSubscribe($user->getemail(), $section, $host);
        }


        return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
    }
}
