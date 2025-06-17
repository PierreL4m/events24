<?php

namespace App\Controller;

use App\Entity\SectionType;
use App\Form\SectionTypeChangeOrderType;
use App\Form\SectionTypeType;
use App\Repository\SectionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/section-type")
 */
class SectionTypeController extends AbstractController
{
    /**
     * @Route("/", name="section_type_index", methods="GET")
     */
    public function index(SectionTypeRepository $sectionTypeRepository): Response
    {
        return $this->render('section_type/index.html.twig', ['section_types' => $sectionTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="section_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $sectionType = new SectionType();
        $form = $this->createForm(SectionTypeType::class, $sectionType, ['new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();


            if (!$sectionType->getMenuTitle()) {
                $sectionType->setMenuTitle($sectionType->getTitle());
            }
            if (!$sectionType->getSectionClass()) {
                $sectionType->setSectionClass('SectionSimple');
            }
            $em->persist($sectionType);
            $em->flush();

            $this->addFlash('success', 'Le type de rubrique a été créé');

            return $this->redirectToRoute('section_type_index');
        }

        return $this->render('section_type/new.html.twig', [
            'section_type' => $sectionType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="section_type_edit", requirements={"id"="\d+"}, methods="GET|POST")
     */
    public function edit(Request $request, SectionType $sectionType): Response
    {
        $form = $this->createForm(SectionTypeType::class, $sectionType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$sectionType->getMenuTitle()) {
                $sectionType->setMenuTitle($sectionType->getTitle());
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le type de rubrique a été modifié');

            return $this->redirectToRoute('section_type_index');
        }

        return $this->render('section_type/edit.html.twig', [
            'section_type' => $sectionType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="section_type_delete", requirements={"id"="\d+"},methods="GET")
     */
    public function delete(Request $request, SectionType $sectionType): Response
    {
        if (count($sectionType->getSections()) == 0) {
            // if ($this->isCsrfTokenValid('delete'.$sectionType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($sectionType);
            $em->flush();
            // }
            $this->addFlash('success', 'Le type de rubrique a été supprimé');

            return $this->redirectToRoute('section_type_index');
        } else {
            throw new \Exception('cannot delete a section type which have sections');
        }
    }


}
