<?php

namespace App\Controller;

use App\Entity\OrganizationType;
use App\Form\OrganizationTypeType;
use App\Repository\OrganizationTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/organization-type")
 */
class OrganizationTypeController extends AbstractController
{
    /**
     * @Route("/", name="organization_type_index", methods="GET")
     */
    public function index(OrganizationTypeRepository $organizationTypeRepository): Response
    {
        return $this->render('organization_type/index.html.twig', ['organization_types' => $organizationTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="organization_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $organizationType = new OrganizationType();
        $form = $this->createForm(OrganizationTypeType::class, $organizationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organizationType);
            $em->flush();

            return $this->redirectToRoute('organization_type_index');
        }

        return $this->render('organization_type/new.html.twig', [
            'organization_type' => $organizationType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_type_show", methods="GET")
     */
    public function show(OrganizationType $organizationType): Response
    {
        return $this->render('organization_type/show.html.twig', ['organization_type' => $organizationType]);
    }

    /**
     * @Route("/{id}/edit", name="organization_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, OrganizationType $organizationType): Response
    {
        $form = $this->createForm(OrganizationTypeType::class, $organizationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('organization_type_index');
        }

        return $this->render('organization_type/edit.html.twig', [
            'organization_type' => $organizationType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_type_delete", methods="DELETE")
     */
    public function delete(Request $request, OrganizationType $organizationType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $organizationType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($organizationType);
            $em->flush();
        }

        return $this->redirectToRoute('organization_type_index');
    }
}
