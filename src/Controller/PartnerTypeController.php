<?php

namespace App\Controller;

use App\Entity\PartnerType;
use App\Form\PartnerTypeType;
use App\Repository\PartnerTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/partner-type")
 */
class PartnerTypeController extends AbstractController
{
    /**
     * @Route("/", name="partner_type_index", methods="GET")
     */
    public function index(PartnerTypeRepository $partnerTypeRepository): Response
    {
        return $this->render('partner_type/index.html.twig', ['partner_types' => $partnerTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="partner_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $partnerType = new PartnerType();
        $form = $this->createForm(PartnerTypeTYpe::class, $partnerType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partnerType);
            $em->flush();

            return $this->redirectToRoute('partner_type_index');
        }

        return $this->render('partner_type/new.html.twig', [
            'partner_type' => $partnerType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partner_type_show", methods="GET")
     */
    public function show(PartnerType $partnerType): Response
    {
        return $this->render('partner_type/show.html.twig', ['partner_type' => $partnerType]);
    }

    /**
     * @Route("/{id}/edit", name="partner_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, PartnerType $partnerType): Response
    {
        $form = $this->createForm(PartnerTypeTYpe::class, $partnerType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('partner_type_index');
        }

        return $this->render('partner_type/edit.html.twig', [
            'partner_type' => $partnerType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partner_type_delete", methods="DELETE")
     */
    public function delete(Request $request, PartnerType $partnerType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partnerType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($partnerType);
            $em->flush();
        }

        return $this->redirectToRoute('partner_type_index');
    }
}
