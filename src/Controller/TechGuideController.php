<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\StandType;
use App\Entity\TechGuide;
use App\Form\StandTypeParticipationType;
use App\Form\TechGuideType;
use App\Helper\GlobalEmHelper;
use App\Repository\TechGuideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/tech-guide")
 */
class TechGuideController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="tech_guide_new", methods="GET|POST")
     */
    public function new(Request $request, Event $event): Response
    {
        $techGuide = new TechGuide();
        $techGuide->setEvent($event);
        $form = $this->createForm(TechGuideType::class, $techGuide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($techGuide);
            $em->flush();

            $this->addFlash('success', 'Le guide technique a été créé');

            return $this->redirectToRoute('tech_file_index', array('id' => $event->getId()));
        }

        return $this->render('tech_guide/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tech_guide_edit", methods="GET|POST")
     */
    public function edit(Request $request, TechGuide $techGuide): Response
    {
        $form = $this->createForm(TechGuideType::class, $techGuide, array('required' => false));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stand_type = $techGuide->getStandType();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le guide technique a été modifié');

            return $this->redirectToRoute('tech_file_index', array('id' => $techGuide->getEvent()->getId()));
        }

        return $this->render('tech_guide/edit.html.twig', [
            'techGuide' => $techGuide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="tech_guide_delete")
     */
    public function delete(Request $request, TechGuide $techGuide): Response
    {
        $id = $techGuide->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();

        $standType = $techGuide->getStandType();

        foreach ($standType->getParticipations() as $p) {
            $p->setStandType(null);
        }

        $em->remove($techGuide);
        $em->flush();

        $this->addFlash('success', 'Le guide technique a été supprimé');

        return $this->redirectToRoute('tech_file_index', array('id' => $id));
    }

    /**
     * @Route("/{id}/download", name="download_tech_guide", methods="GET", requirements={"id" = "\d+"})
     */
    public function download(TechGuide $tech_guide): Response
    {
        $path = $this->getParameter('kernel.project_dir') . '/public/uploads/tech_guide/' . $tech_guide->getPath();

        if (file_exists($path) && is_file($path)) {
            return $this->file($path);
        } else {
            throw new \Exception('Please download again "Cahier des charges de base"');
        }

    }

    /**
     * @Route("/{id}/add-participation", name="tech_guide_add_participation", methods="GET|POST")
     */
    public function addParticipation(Request $request, StandType $stand_type, GlobalEmHelper $em_helper): Response
    {
        $original_entities = $em_helper->backupOriginalEntities($stand_type->getParticipations());

        $form = $this->createForm(StandTypeParticipationType::class, $stand_type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em_helper->removeRelation($original_entities, $stand_type, $stand_type->getParticipations(), 'removeParticipation');

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Les exposants ont été ajoutés');

            return $this->redirectToRoute('tech_file_index', array('id' => $stand_type->getTechGuide()->getEvent()->getId()));
        }

        return $this->render('tech_guide/addParticipation.html.twig', [
            'standType' => $stand_type,
            'form' => $form->createView(),
        ]);
    }
}
