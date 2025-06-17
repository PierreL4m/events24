<?php

namespace App\Controller;

use App\Entity\Street;
use App\Form\StreetType;
use App\Repository\StreetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/street")
 */
class StreetController extends AbstractController
{
    /**
     * @Route("/", name="street_index", methods="GET")
     */
    public function index(StreetRepository $streetRepository): Response
    {
        return $this->render('street/index.html.twig', ['streets' => $streetRepository->findAll()]);
    }

    /**
     * @Route("/new", name="street_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $street = new Street();
        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($street);
            $em->flush();

            $this->addFlash(
                'success',
                'La voie a été créée'
            );


            return $this->redirectToRoute('street_index');
        }

        return $this->render('street/new.html.twig', [
            'street' => $street,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="street_edit", methods="GET|POST")
     */
    public function edit(Request $request, Street $street): Response
    {
        $form = $this->createForm(StreetType::class, $street);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'La voie a été modifiée'
            );

            return $this->redirectToRoute('street_index');
        }

        return $this->render('street/edit.html.twig', [
            'street' => $street,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="street_delete", methods="DELETE")
     */
    public function delete(Request $request, Street $street): Response
    {
        if ($this->isCsrfTokenValid('delete' . $street->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($street);
            $em->flush();
        }

        return $this->redirectToRoute('street_index');
    }
}
