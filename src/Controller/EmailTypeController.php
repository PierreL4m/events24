<?php

namespace App\Controller;

use App\Entity\EmailType;
use App\Form\EmailTypeType;
use App\Repository\EmailTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/email-type")
 */
class EmailTypeController extends AbstractController
{
    /**
     * @Route("/", name="email_type_index", methods="GET")
     */
    public function index(EmailTypeRepository $emailTypeRepository): Response
    {
        return $this->render('email_type/index.html.twig', ['email_types' => $emailTypeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="email_type_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $emailType = new EmailType();
        $form = $this->createForm(EmailTypeType::class, $emailType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($emailType);
            $em->flush();

            return $this->redirectToRoute('email_type_index');
        }

        return $this->render('email_type/new.html.twig', [
            'email_type' => $emailType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="email_type_show", methods="GET")
     */
    public function show(EmailType $emailType): Response
    {
        return $this->render('email_type/show.html.twig', ['email_type' => $emailType]);
    }

    /**
     * @Route("/{id}/edit", name="email_type_edit", methods="GET|POST")
     */
    public function edit(Request $request, EmailType $emailType): Response
    {
        $form = $this->createForm(EmailTypeType::class, $emailType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('email_type_show', ['id' => $emailType->getId()]);
        }

        return $this->render('email_type/edit.html.twig', [
            'email_type' => $emailType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="email_type_delete", methods="DELETE")
     */
    public function delete(Request $request, EmailType $emailType): Response
    {
        if ($this->isCsrfTokenValid('delete' . $emailType->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($emailType);
            $em->flush();
        }

        return $this->redirectToRoute('email_type_index');
    }
}
