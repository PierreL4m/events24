<?php

namespace App\Controller;

use App\Entity\BilanFileType;
use App\Form\BilanFileTypeType;
use App\Repository\BilanFileTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bilan/file/type")
 */
class BilanFileTypeController extends AbstractController
{
    /**
     * @Route("/", name="bilan_file_type_index", methods="GET")
     */
    public function index(BilanFileTypeRepository $bilanFileTypeRepository): Response
    {
        return $this->render('bilan_file_type/index.html.twig', ['bilan_file_types' => $bilanFileTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="bilan_file_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $bilanFileType = new BilanFileType();
        $form = $this->createForm(BilanFileTypeType::class, $bilanFileType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bilanFileType);
            $em->flush();

            return $this->redirectToRoute('bilan_file_type_index');
        }

        return $this->render('bilan_file_type/new.html.twig', [
            'bilan_file_type' => $bilanFileType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bilan_file_type_show", methods="GET")
     */
    public function show(BilanFileType $bilanFileType): Response
    {
        return $this->render('bilan_file_type/show.html.twig', ['bilan_file_type' => $bilanFileType]);
    }

    /**
     * @Route("/{id}/edit", name="bilan_file_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, BilanFileType $bilanFileType): Response
    {
        $form = $this->createForm(BilanFileTypeType::class, $bilanFileType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bilan_file_type_index');
        }

        return $this->render('bilan_file_type/edit.html.twig', [
            'bilan_file_type' => $bilanFileType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bilan_file_type_delete", methods="DELETE")
     */
    public function delete(Request $request, BilanFileType $bilanFileType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bilanFileType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bilanFileType);
            $em->flush();
        }

        return $this->redirectToRoute('bilan_file_type_index');
    }
}
